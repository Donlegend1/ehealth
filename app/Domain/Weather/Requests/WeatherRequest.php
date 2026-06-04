<?php

namespace App\Domain\Weather\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Weather\DTOs\WeatherQueryDTO;

class WeatherRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * Convert validation parameters to WeatherQueryDTO.
     */
    public function toDto(): WeatherQueryDTO
    {
        return WeatherQueryDTO::fromRequest($this->validated());
    }
}
