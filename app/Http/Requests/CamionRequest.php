<?php

namespace App\Http\Requests;

use App\Models\Camion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CamionRequest
 *
 * Form request for validating truck data
 */
class CamionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'placa' => [
                'required',
                'string',
                'max:20',
                Rule::unique('camiones', 'placa')->ignore($this->camion)
            ],
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'year' => 'required|integer|between:1900,' . (date('Y') + 1),
            'numero_motor' => 'nullable|string|max:50',
            'kilometraje_actual' => 'required|numeric|min:0|max:9999999.99',
            'intervalo_mantenimiento_km' => 'required|integer|between:1000,50000',
            'estado' => ['required', Rule::in(Camion::ESTADOS)],
        ];

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'placa.required' => 'La placa es obligatoria',
            'placa.unique' => 'Ya existe un camión con esta placa',
            'placa.max' => 'La placa no puede tener más de 20 caracteres',
            'marca.required' => 'La marca es obligatoria',
            'marca.max' => 'La marca no puede tener más de 50 caracteres',
            'modelo.required' => 'El modelo es obligatorio',
            'modelo.max' => 'El modelo no puede tener más de 50 caracteres',
            'year.required' => 'El año es obligatorio',
            'year.integer' => 'El año debe ser un número entero',
            'year.between' => 'El año debe estar entre 1900 y ' . (date('Y') + 1),
            'numero_motor.max' => 'El número de motor no puede tener más de 50 caracteres',
            'kilometraje_actual.required' => 'El kilometraje actual es obligatorio',
            'kilometraje_actual.numeric' => 'El kilometraje debe ser un número',
            'kilometraje_actual.min' => 'El kilometraje no puede ser negativo',
            'kilometraje_actual.max' => 'El kilometraje no puede ser mayor a 9,999,999.99',
            'intervalo_mantenimiento_km.required' => 'El intervalo de mantenimiento es obligatorio',
            'intervalo_mantenimiento_km.integer' => 'El intervalo debe ser un número entero',
            'intervalo_mantenimiento_km.between' => 'El intervalo debe estar entre 1,000 y 50,000 km',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado seleccionado no es válido',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'placa' => 'placa',
            'marca' => 'marca',
            'modelo' => 'modelo',
            'year' => 'año',
            'numero_motor' => 'número de motor',
            'kilometraje_actual' => 'kilometraje actual',
            'intervalo_mantenimiento_km' => 'intervalo de mantenimiento',
            'estado' => 'estado',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate that year is reasonable for the brand/model
            if ($this->year && $this->year < 1980) {
                $validator->errors()->add('year', 'El año parece demasiado antiguo para un camión comercial');
            }

            // Validate reasonable mileage for the year
            if ($this->year && $this->kilometraje_actual) {
                $currentYear = date('Y');
                $vehicleAge = $currentYear - $this->year;
                $expectedMaxMileage = $vehicleAge * 100000; // 100k km per year max

                if ($this->kilometraje_actual > $expectedMaxMileage) {
                    $validator->errors()->add('kilometraje_actual', 
                        'El kilometraje parece excesivo para un vehículo de ' . $vehicleAge . ' años');
                }
            }

            // Validate maintenance interval based on truck type/year
            if ($this->intervalo_mantenimiento_km && $this->year) {
                if ($this->year < 2000 && $this->intervalo_mantenimiento_km > 15000) {
                    $validator->errors()->add('intervalo_mantenimiento_km', 
                        'Los camiones más antiguos requieren intervalos de mantenimiento más cortos');
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Clean and format the license plate
        if ($this->placa) {
            $this->merge([
                'placa' => strtoupper(trim($this->placa))
            ]);
        }

        // Clean and format brand and model
        if ($this->marca) {
            $this->merge([
                'marca' => ucwords(strtolower(trim($this->marca)))
            ]);
        }

        if ($this->modelo) {
            $this->merge([
                'modelo' => ucwords(strtolower(trim($this->modelo)))
            ]);
        }

        // Clean motor number
        if ($this->numero_motor) {
            $this->merge([
                'numero_motor' => strtoupper(trim($this->numero_motor))
            ]);
        }

        // Ensure numeric fields are properly formatted
        if ($this->kilometraje_actual) {
            $this->merge([
                'kilometraje_actual' => (float) $this->kilometraje_actual
            ]);
        }

        if ($this->intervalo_mantenimiento_km) {
            $this->merge([
                'intervalo_mantenimiento_km' => (int) $this->intervalo_mantenimiento_km
            ]);
        }
    }
}