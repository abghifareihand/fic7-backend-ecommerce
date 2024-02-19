<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Midtrans\CallbackService;
use Illuminate\Http\Request;
use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class CallbackController extends Controller
{

    public function sendNotificationToUser($userId, $message)
    {
        // Dapatkan FCM token user dari table 'users'
        $user = User::find($userId);
        $token = $user->fcm_token;

        // Kirim notifikasi ke perangkat android
        $messaging = app('firebase.messaging');
        $notification = Notification::create('Order Terbayar', $message);

        $message = CloudMessage::withTarget('token', $token)->withNotification($notification);

        $messaging->send($message);

    }

    public function callback()
    {
        $callback = new CallbackService;

            $notification = $callback->getNotification();
            $order = $callback->getOrder();

            if ($callback->isSuccess()) {
                Order::where('id', $order->id)->update([
                    'payment_status' => 2,
                ]);
                $this->sendNotificationToUser($order->seller_id, 'Order ' . $order->total_price . ' telah dibayarkan');
            }

            if ($callback->isExpire()) {
                Order::where('id', $order->id)->update([
                    'payment_status' => 3,
                ]);
            }

            if ($callback->isCancelled()) {
                Order::where('id', $order->id)->update([
                    'payment_status' => 3,
                ]);
            }



            return response()->json([
             'success' => true,
             'message' => 'Notification successfully processed',
            ]);

        }
    }
