<?php

namespace App\Http\Requests\Web\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyEmployeeRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    
    protected function prepareForValidation(): void{
        $this->merge([
            'phone' => $this->phone
                ? preg_replace('/[\s\-_()]/', '', $this->phone)
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
            'phone' => [
                'required',
                'regex:/^\+998\d{9}$/',
                'unique:users,phone',
            ],
            'role' => [
                'required',
                Rule::in(['director', 'courier']),
            ],
        ];
    }

    public function messages(): array{
        return [
            'company_id.exists' => 'Kompaniya topilmadi.',
            'name.required' => 'Hodim ismini kiriting.',
            'phone.regex' => 'Telefon +998901234567 ko‘rinishida bo‘lishi kerak.',
            'phone.unique' => 'Bu telefon raqam bilan foydalanuvchi mavjud.',
            'role.in' => 'Lavozim noto‘g‘ri tanlangan.',
        ];
    }
    
}
