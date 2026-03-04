<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource{
    public function toArray(Request $request): array{
        return [
            'id' => $this->id,
            'name' => $this->company_name,
            'rating' => $this->rating,
            'rating_count' => $this->rating_count,
            'logo' => $this->logo ? asset($this->logo) : null,
            'banner' => $this->banner ? asset($this->banner) : null,
            'working_hours' => $this->working_hours,
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
