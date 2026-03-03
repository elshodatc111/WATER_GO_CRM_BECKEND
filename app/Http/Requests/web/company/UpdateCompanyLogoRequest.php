<?php

namespace App\Http\Requests\Web\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyLogoRequest extends FormRequest{
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
            'logo' => [
                'required',
                'image',
                'mimes:jpg,jpeg',
                'dimensions:width=256,height=256',
                'max:2048', // 2MB
            ],
        ];
    }

    public function messages(): array{
        return [
            'logo.required' => 'Logotip yuklash majburiy.',
            'logo.mimes' => 'Logotip faqat JPG formatda bo‘lishi kerak.',
            'logo.dimensions' => 'Logotip o‘lchami 256x256 bo‘lishi kerak.',
            'logo.max' => 'Logotip hajmi 2MB dan oshmasligi kerak.',
            'id.exists' => 'Kompaniya topilmadi.',
        ];
    }
}
