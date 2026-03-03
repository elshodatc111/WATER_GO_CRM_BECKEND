<?php

namespace App\Http\Requests\web\product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductBannerRequest extends FormRequest{

    public function authorize(): bool{
        return true;
    }

    public function rules(): array{
        return [
            'id' => ['required','integer',Rule::exists('products', 'id'),],
            'image_banner' => ['required','image','mimes:jpg,jpeg,png','dimensions:width=1080,height=512','max:4096',],
        ];
    }

    public function messages(): array{
        return [
            'image_banner.required' => 'Banner yuklash majburiy.',
            'image_banner.mimes' => 'Faqat JPG yoki PNG ruxsat etiladi.',
            'image_banner.dimensions' => 'Banner 1080x512 bo‘lishi kerak.',
            'image_banner.max' => 'Banner 4MB dan oshmasligi kerak.',
            'id.exists' => 'Mahsulot topilmadi.',
        ];
    }
}
