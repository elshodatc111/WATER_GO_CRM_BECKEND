<?php

namespace App\Http\Requests\web\company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepositCompanyBalanceRequest extends FormRequest{

    public function authorize(): bool{
        return auth()->check() && auth()->user()->role === 'admin';
    }

    protected function prepareForValidation(): void{
        $this->merge([
            'amount' => $this->amount ? str_replace(' ', '', $this->amount) : null,
        ]);
    }

    public function rules(): array{
        return [
            'company_id' => ['required','integer',Rule::exists('companies', 'id'),],
            'type' => ['required',Rule::in(['naqt', 'card', 'return']),],
            'amount' => ['required','numeric','min:1',],
            'description' => ['nullable','string','max:1000',],
        ];
    }

    public function messages(): array{
        return [
            'company_id.exists' => 'Firma topilmadi.',
            'type.required' => 'To‘lov turini tanlang.',
            'type.in' => 'To‘lov turi noto‘g‘ri.',
            'amount.required' => 'Miqdor kiriting.',
            'amount.numeric' => 'Miqdor faqat son bo‘lishi kerak.',
            'amount.min' => 'Miqdor kamida 1 so‘m bo‘lishi kerak.',
        ];
    }
}
