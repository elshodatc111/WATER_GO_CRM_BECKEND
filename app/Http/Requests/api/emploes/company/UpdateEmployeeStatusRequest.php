<?php

namespace App\Http\Requests\api\emploes\company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeStatusRequest extends FormRequest{
    public function authorize(): bool{
        return auth()->check() && auth()->user()->role === 'director';
    }
    public function rules(): array{
        return [
            'employee_id' => ['required', 'exists:users,id'],
            'is_active'   => ['required', 'boolean'],
        ];
    }
    public function messages(): array{
        return [
            'employee_id.exists' => 'Bunday xodim topilmadi.',
            'is_active.required' => 'Statusni yuborish majburiy (true/false).',
        ];
    }
}
