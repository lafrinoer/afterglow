<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['ticket_tier_id' => ['required', 'integer', 'exists:ticket_tiers,id'], 'quantity' => ['required', 'integer', 'min:1', 'max:6'], 'day_pass' => ['sometimes', 'boolean']];
    }
}
