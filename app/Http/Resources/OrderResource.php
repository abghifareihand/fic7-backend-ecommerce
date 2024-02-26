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
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'seller_id' => $this->seller_id,
            'number' => $this->number,
            'total_price' => $this->total_price,
            'payment_status' => $this->payment_status,
            'delivery_address' => $this->delivery_address,
            'payment_url' => $this->payment_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'orderItems' => OrderItemResource::collection($this->whenLoaded('orderItems')),
        ];
    }
}
