<?php

namespace App\Http\Requests\web\auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    
    protected function prepareForValidation(){
        if ($this->phone) {
            $this->merge([
                'phone' => str_replace([' ', '(', ')', '-'], '', $this->phone),
            ]);
        }
    }

    public function rules(): array{
        return [
            'phone' => ['required', 'string', 'size:13'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages(): array{
        return [
            'phone.required' => 'Telefon raqamini kiriting.',
            'phone.size' => 'Telefon raqami noto\'g\'ri kiritildi.',
            'password.required' => 'Parolni kiriting.',
            'password.min' => 'Parol kamida 8 ta belgidan iborat bo\'lishi kerak.',
        ];
    }
}
