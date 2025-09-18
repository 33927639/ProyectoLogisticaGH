<?php

namespace App\Http\Requests;

use App\Models\Ruta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class RutaRequest
 *
 * Form request for validating route data
 */
class RutaRequest extends FormRequest
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
            'origen' => 'required|string|max:255',
            'destino' => 'required|string|max:255|different:origen',
            'distancia_km' => 'required|numeric|min:0.1|max:9999.99',
            'tiempo_estimado_horas' => 'required|numeric|min:0.1|max:99.99',
            'descripcion' => 'nullable|string|max:1000',
            'estado' => ['required', Rule::in(Ruta::ESTADOS)],
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
            'origen.required' => 'El origen es obligatorio',
            'origen.max' => 'El origen no puede tener más de 255 caracteres',
            'destino.required' => 'El destino es obligatorio',
            'destino.max' => 'El destino no puede tener más de 255 caracteres',
            'destino.different' => 'El destino debe ser diferente al origen',
            'distancia_km.required' => 'La distancia es obligatoria',
            'distancia_km.numeric' => 'La distancia debe ser un número',
            'distancia_km.min' => 'La distancia debe ser mayor a 0.1 km',
            'distancia_km.max' => 'La distancia no puede ser mayor a 9,999.99 km',
            'tiempo_estimado_horas.required' => 'El tiempo estimado es obligatorio',
            'tiempo_estimado_horas.numeric' => 'El tiempo estimado debe ser un número',
            'tiempo_estimado_horas.min' => 'El tiempo estimado debe ser mayor a 0.1 horas',
            'tiempo_estimado_horas.max' => 'El tiempo estimado no puede ser mayor a 99.99 horas',
            'descripcion.max' => 'La descripción no puede tener más de 1000 caracteres',
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
            'origen' => 'origen',
            'destino' => 'destino',
            'distancia_km' => 'distancia',
            'tiempo_estimado_horas' => 'tiempo estimado',
            'descripcion' => 'descripción',
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
            // Check for duplicate routes (same origin and destination)
            if ($this->origen && $this->destino) {
                $existingRoute = \App\Models\Ruta::where('origen', $this->origen)
                    ->where('destino', $this->destino)
                    ->when($this->route('ruta'), function ($query) {
                        return $query->where('id', '!=', $this->route('ruta')->id);
                    })
                    ->first();

                if ($existingRoute) {
                    $validator->errors()->add('destino', 'Ya existe una ruta con este origen y destino');
                }
            }

            // Validate reasonable time based on distance
            if ($this->distancia_km && $this->tiempo_estimado_horas) {
                $velocidadPromedio = $this->distancia_km / $this->tiempo_estimado_horas;
                
                // Check if speed is too slow (less than 20 km/h)
                if ($velocidadPromedio < 20) {
                    $validator->errors()->add('tiempo_estimado_horas', 
                        'El tiempo estimado parece demasiado largo para la distancia (velocidad < 20 km/h)');
                }
                
                // Check if speed is too fast (more than 120 km/h for trucks)
                if ($velocidadPromedio > 120) {
                    $validator->errors()->add('tiempo_estimado_horas', 
                        'El tiempo estimado parece demasiado corto para la distancia (velocidad > 120 km/h)');
                }
            }

            // Validate city names (only letters, spaces, and common punctuation)
            if ($this->origen && !preg_match('/^[a-zA-ZáéíóúñÑ\s\.\-,]+$/u', $this->origen)) {
                $validator->errors()->add('origen', 'El origen contiene caracteres no válidos');
            }

            if ($this->destino && !preg_match('/^[a-zA-ZáéíóúñÑ\s\.\-,]+$/u', $this->destino)) {
                $validator->errors()->add('destino', 'El destino contiene caracteres no válidos');
            }

            // Warn about very long routes
            if ($this->distancia_km && $this->distancia_km > 1000) {
                // This is just a warning, not an error, but we could add it to a warnings array
                // For now, we'll just log it
                \Log::info('Very long route being created', [
                    'origen' => $this->origen,
                    'destino' => $this->destino,
                    'distancia' => $this->distancia_km
                ]);
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
        // Clean and format city names
        if ($this->origen) {
            $this->merge([
                'origen' => ucwords(strtolower(trim($this->origen)))
            ]);
        }

        if ($this->destino) {
            $this->merge([
                'destino' => ucwords(strtolower(trim($this->destino)))
            ]);
        }

        // Ensure numeric fields are properly formatted
        if ($this->distancia_km) {
            $this->merge([
                'distancia_km' => (float) $this->distancia_km
            ]);
        }

        if ($this->tiempo_estimado_horas) {
            $this->merge([
                'tiempo_estimado_horas' => (float) $this->tiempo_estimado_horas
            ]);
        }

        // Clean description
        if ($this->descripcion) {
            $this->merge([
                'descripcion' => trim($this->descripcion) ?: null
            ]);
        }
    }
}