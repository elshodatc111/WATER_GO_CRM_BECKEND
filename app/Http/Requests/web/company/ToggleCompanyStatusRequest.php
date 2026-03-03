<?php

namespace App\Http\Requests\Web\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ToggleCompanyStatusRequest extends FormRequest{
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
        ];
    }

    public function messages(): array{
        return [
            'id.required' => 'Kompaniya ID topilmadi.',
            'id.exists' => 'Kompaniya mavjud emas.',
        ];
    }
}
