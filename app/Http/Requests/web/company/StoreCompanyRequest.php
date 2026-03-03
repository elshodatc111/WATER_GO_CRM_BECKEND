<?php

namespace App\Http\Requests\web\company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    protected function prepareForValidation(): void{
        $this->merge([
            'phone' => $this->phone
                ? preg_replace('/[\s\-_()]/', '', $this->phone)
                : null,
            'service_fee' => $this->service_fee
                ? str_replace(' ', '', $this->service_fee)
                : null,
            'inn' => $this->inn
                ? preg_replace('/[\s_]/', '', $this->inn)
                : null,
        ]);
    }
    public function rules(): array{
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'direktor' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'regex:/^\+998\d{9}$/',
                'unique:companies,phone',
            ],
            'working_hours' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'service_fee' => ['required', 'numeric', 'min:0'],
            'inn' => [
                'required',
                'string',
                'size:10',
                'unique:companies,inn',
            ],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'delivery_radius' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string'],
            'logo' => [
                'required',
                'image',
                'mimes:jpg,jpeg',
                'dimensions:width=256,height=256',
                'max:2048',
            ],
            'banner' => [
                'required',
                'image',
                'mimes:jpg,jpeg',
                'dimensions:width=1920,height=1080',
                'max:4096',
            ],
        ];
    }

    public function messages(): array{
        return [
            'phone.regex' => 'Telefon raqam +998901234567 ko‘rinishida bo‘lishi kerak.',
            'phone.unique' => 'Bu telefon raqam bilan firma allaqachon mavjud.',
            'inn.size' => 'INN 14 ta belgidan iborat bo‘lishi kerak.',
            'inn.unique' => 'Bu INN bilan firma allaqachon mavjud.',
            'logo.mimes' => 'Logotip faqat JPG formatda bo‘lishi kerak.',
            'logo.dimensions' => 'Logotip o‘lchami 128x128 bo‘lishi kerak.',
            'banner.mimes' => 'Banner faqat JPG formatda bo‘lishi kerak.',
            'banner.dimensions' => 'Banner o‘lchami 1920x1080 bo‘lishi kerak.',
        ];
    }
    
}
