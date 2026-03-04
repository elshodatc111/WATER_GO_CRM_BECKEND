<?php

namespace App\Http\Requests\api\emploes\company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductStatusRequest extends FormRequest{

    public function authorize(): bool{
        return auth()->check() && auth()->user()->role === 'director';
    }

    public function rules(): array{
        return [
            'product_id' => ['required', 'exists:products,id'],
            'is_active'   => ['required', 'boolean'],
        ];
    }
    public function messages(): array{
        return [
            'product_id.exists' => 'Bunday maxsulot topilmadi.',
            'is_active.required' => 'Statusni yuborish majburiy (true/false).',
        ];
    }
}
