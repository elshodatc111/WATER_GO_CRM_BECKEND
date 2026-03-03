<?php

namespace App\Http\Requests\api\emploes\auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest{

    public function authorize(): bool{
        return true;
    }

    protected function prepareForValidation(): void{
        $this->merge([
            'phone' => str_replace([' ', '(', ')', '-'], '', $this->phone)
        ]);
    }

    public function rules(): array{
        return [
            'phone' => ['required','string','size:13'],
            'password' => ['required','string','min:8'],
        ];
    }
}
