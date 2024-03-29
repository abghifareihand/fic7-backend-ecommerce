<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Midtrans\CreatePaymentUrlService;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class OrderController extends Controller
{
    public function sendNotificationToUser($userId, $message)
    {
        // Dapatkan FCM token user dari table 'users'
        $user = User::find($userId);
        $userName = $user->name;
        $token = $user->fcm_token;

        // Kirim notifikasi ke perangkat android
        $messaging = app('firebase.messaging');
        $notification = Notification::create('Order masuk', $message);

        $message = CloudMessage::withTarget('token', $token)->withNotification($notification);

        $messaging->send($message);

    }

    public function order(Request $request)
    {
        $order = Order::create([
            'user_id' => $request->user()->id,
            'seller_id' => $request->seller_id,
            'number' => time(),
            'total_price' => $request->total_price,
            'payment_status' => 1,
            'delivery_address' => $request->delivery_address,
        ]);

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
            ]);
        }

        // manggil service midtrans dapatkan payment url
        $this->sendNotificationToUser($request->seller_id, 'Order Rp.' . number_format($request->total_price) . ' masuk, menunggu pembayaran');
        $midtrans = new CreatePaymentUrlService();
        $paymentUrl = $midtrans->getPaymentUrl($order->load('user', 'orderItems'));
        $order->update([
            'payment_url' => $paymentUrl,
        ]);
        return response()->json([
            'data' => $order,
        ]);
    }

    public function orderById(Request $request)
{
    $user_id = $request->query('user_id');
    $seller_id = $request->query('seller_id');
    $payment_status = $request->query('payment_status');

    $query = Order::when(
        $user_id,
        fn ($query, $user_id) => $query->where('user_id', $user_id)
    )->when(
        $seller_id,
        fn ($query, $seller_id) => $query->where('seller_id', $seller_id)
    )->when(
        $payment_status,
        fn ($query, $payment_status) => $query->where('payment_status', $payment_status)
    )->with('orderItems.product');

    // Cek apakah hanya satu pesanan yang diinginkan atau semua pesanan
    if ($request->has('single') && $request->single) {
        $order = $query->first(); // Ambil satu pesanan
        if ($order) {
            return new OrderResource($order);
        } else {
            return response()->json(['message' => 'Order not found'], 404);
        }
    } else {
        $orders = $query->get(); // Ambil semua pesanan
        return OrderResource::collection($orders);
    }
}


}
