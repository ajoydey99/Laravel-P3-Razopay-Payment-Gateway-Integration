<?php
namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class PaymentFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'   => 'required|string|max:255|min:5',
            'email'  => 'required|email|max:50',
            'phone'  => 'required|string|min:10|max:20',
            'amount' => 'required|numeric|min:1|max:100000',
        ];
    }
}