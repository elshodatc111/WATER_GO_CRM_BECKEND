<?php

namespace App\Http\Requests\api\emploes\company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEmploesRequest extends FormRequest{
    
    public function authorize(): bool{
        return auth()->check() && auth()->user()->role === 'director';
    }

    protected function prepareForValidation(): void{
        if ($this->phone) {
            $this->merge(['phone' => preg_replace('/[^0-9+]/', '', $this->phone),]);
        }
    }

    public function rules(): array    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['required','regex:/^\+998\d{9}$/',Rule::unique('users', 'phone')->whereNull('deleted_at'),],
            'role' => ['required', Rule::in(['director', 'courier'])],
        ];
    }

    public function messages(): array{
        return [
            'name.required' => 'Xodim ismini kiriting.',
            'name.min' => 'Ism kamida 3 ta harf bo‘lishi kerak.',
            'phone.required' => 'Telefon raqamni kiriting.',
            'phone.regex' => 'Telefon raqam +998901234567 ko‘rinishida bo‘lishi shart.',
            'phone.unique' => 'Bu telefon raqam allaqachon ro‘yxatdan o‘tgan.',
            'role.required' => 'Lavozimni tanlang.',
            'role.in' => 'Faqat direktor yoki kuryer lavozimini tanlash mumkin.',
        ];
    }
}
