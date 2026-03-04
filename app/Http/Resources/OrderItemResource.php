<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource{
    public function toArray(Request $request): array{
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product->name ?? 'O\'chirilgan mahsulot',
            'product_image' => $this->product && $this->product->image ? asset($this->product->image) : null,
            'quantity' => $this->quantity,
            'price_at_order' => (float) $this->price,
            'total_price' => (float) $this->total,
        ];
    }
}
