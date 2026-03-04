<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource{
    public function toArray(Request $request): array{
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'image' => $this->image ? asset($this->image) : null,
            'image_banner' => $this->image_banner ? asset($this->image_banner) : null,
            'is_active' => $this->is_active,
        ];
    }
}
