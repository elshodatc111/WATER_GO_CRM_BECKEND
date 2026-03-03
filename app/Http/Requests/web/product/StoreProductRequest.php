<?php

namespace App\Http\Requests\Web\product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest{
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
            'company_id' => [
                'required',
                'integer',
                Rule::exists('companies', 'id'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'dimensions:width=256,height=256',
                'max:2048'
            ],
            'image_banner' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'dimensions:width=1080,height=512',
                'max:4096'
            ],
        ];
    }

    public function messages(): array{
        return [
            'company_id.exists' => 'Kompaniya topilmadi.',
            'image.dimensions' => 'Rasm 256x256 bo‘lishi kerak.',
            'image_banner.dimensions' => 'Banner 1080x512 bo‘lishi kerak.',
            'price.numeric' => 'Narx faqat son bo‘lishi kerak.',
        ];
    }
}
