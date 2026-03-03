<?php

namespace App\Http\Requests\Web\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyBannerRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }

    public function rules(): array{
        return [
            'id' => [
                'required',
                'integer',
                Rule::exists('companies', 'id'),
            ],
            'banner' => [
                'required',
                'image',
                'mimes:jpg,jpeg',
                'dimensions:width=1920,height=1080',
                'max:4096', // 4MB
            ],
        ];
    }

    public function messages(): array{
        return [
            'banner.required' => 'Banner yuklash majburiy.',
            'banner.mimes' => 'Banner faqat JPG formatda bo‘lishi kerak.',
            'banner.dimensions' => 'Banner o‘lchami 1920x1080 bo‘lishi kerak.',
            'banner.max' => 'Banner hajmi 4MB dan oshmasligi kerak.',
            'id.exists' => 'Kompaniya topilmadi.',
        ];
    }
}
