<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ***
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:h:i a',
            'session_number' => 'required|integer|min:1',
            'contracted_treatment_id' => 'required|integer|exists:contracted_treatments,id',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('appointment_time')) {
            $this->merge([
                'appointment_time' => str_replace(['a. m.', 'p. m.'], ['am', 'pm'], $this->appointment_time),
            ]);
        }
    }
}
