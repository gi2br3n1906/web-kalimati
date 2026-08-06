<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Models\IotDevice;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDeviceTelemetryRequest extends FormRequest
{
    private ?IotDevice $authenticatedDevice = null;

    public function authorize(): bool
    {
        $this->authenticatedDevice = IotDevice::findActiveByToken(
            trim((string) $this->header('X-Device-Token')),
        );

        return $this->authenticatedDevice !== null;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'temp_air' => ['required', 'numeric', 'between:-50,80'],
            'hum_air' => ['required', 'numeric', 'between:0,100'],
            'temp_soil' => ['required', 'numeric', 'between:-20,80'],
            'hum_soil_percent' => ['required', 'numeric', 'between:0,100'],
            'raw_soil' => ['required', 'integer', 'between:0,65535'],
            'lux_light' => ['required', 'numeric', 'between:0,200000'],
        ];
    }

    public function device(): IotDevice
    {
        return $this->authenticatedDevice ?? throw new \LogicException('IoT device is not authenticated.');
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
