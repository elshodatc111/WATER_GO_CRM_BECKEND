<?php
namespace App\Http\Requests\web\product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductImageRequest extends FormRequest{

    public function authorize(): bool{
        return true;
    }

    public function rules(): array{
        return [
            'id' => ['required','integer',Rule::exists('products', 'id'),],
            'image' => ['required','image','mimes:jpg,jpeg,png','dimensions:width=256,height=256','max:2048',],
        ];
    }

    public function messages(): array{
        return [
            'image.dimensions' => 'Rasm 256x256 bo‘lishi kerak.',
            'image.mimes' => 'Faqat JPG yoki PNG ruxsat etiladi.',
        ];
    }
    
}
