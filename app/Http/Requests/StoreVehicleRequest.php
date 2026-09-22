<?php

namespace App\Http\Requests;

use App\Enums\VehicleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('vehicle_number')) {
            $this->merge([
                'vehicle_number' => preg_replace('/\s+/', '', trim((string) $this->input('vehicle_number'))),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'vehicle_model' => ['required', 'string', 'max:255'],
            'vehicle_number' => ['required', 'string', 'max:50', 'unique:vehicles,vehicle_number'],
            'vehicle_type' => ['required', Rule::enum(VehicleType::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'vehicle_number.unique' => 'This vehicle number is already registered.',
        ];
    }
}
