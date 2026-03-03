<?php

namespace App\Http\Requests\web\product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest{
    
    public function authorize(): bool{
        return true;
    }

    protected function prepareForValidation(): void{
        $this->merge([
            'price' => $this->price
                ? str_replace(' ', '', $this->price)
                : null,
        ]);
    }

    public function rules(): array{
        return [
            'id' => ['required','integer', Rule::exists('products', 'id'),],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array{
        return [
            'id.exists' => 'Mahsulot topilmadi.',
            'price.numeric' => 'Narx faqat son bo‘lishi kerak.',
        ];
    }
}
