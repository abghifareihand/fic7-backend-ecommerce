<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'number' => $this->number,
            'total_price' => $this->total_price,
            'payment_status' => $this->payment_status,
            'payment_url' => $this->payment_url,
            'delivery_address' => $this->delivery_address,
            'seller_id' => $this->seller_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'orderItems' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
