<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource{
    
    public function toArray(Request $request): array{
        return [
            'id' => $this->id,
            'company_name' => $this->company->company_name ?? 'Noma\'lum',
            'total_count' => $this->total_count,
            'total_price' => (float) $this->total_price,
            'delivery_price' => (float) $this->delivery_price,
            'final_price' => (float) $this->final_price,
            'status' => $this->status, // pending, qabul_qilindi va h.k.
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'address' => $this->address,
            'courier_comment' => $this->courier_comment,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'client' => [
                'name' => $this->user->name,
                'phone' => $this->user->phone,
            ],
            'location' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'address' => $this->address,
            ],
        ];
    }
}
