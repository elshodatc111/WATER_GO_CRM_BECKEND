<?php

namespace App\Http\Requests\api\emploes\company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyStatusRequest extends FormRequest{
    public function authorize(): bool{
        return auth()->check() && auth()->user()->role === 'director';
    }
    public function rules(): array{
        return [
            'is_active'   => ['required', 'boolean'],
        ];
    }
    public function messages(): array{
        return [
            'is_active.required' => 'Statusni yuborish majburiy (true/false).',
        ];
    }
}
