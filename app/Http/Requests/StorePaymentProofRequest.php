<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048']];
    }
}
