<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreIotTelemetryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $secret = (string) config('services.iot.webhook_secret');

        return hash_equals(
            $secret,
            (string) $this->header('X-IoT-Device-Token'),
        ) && $secret !== '';
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'device_id' => ['required', 'string', 'max:100'],
            'grid_code' => ['required', 'string', 'exists:land_grids,grid_code'],
            'ph_level' => ['required', 'numeric', 'between:0,14'],
            'moisture_percentage' => ['required', 'numeric', 'between:0,100'],
            'temperature_celsius' => ['required', 'numeric', 'between:-99.99,99.99'],
            'recorded_at' => ['required', 'date'],
        ];
    }

    protected function failedAuthorization(): never
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Unauthorized.',
        ], 401));
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
