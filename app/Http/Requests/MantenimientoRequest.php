<?php

namespace App\Http\Requests;

use App\Models\Mantenimiento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class MantenimientoRequest
 *
 * Form request for validating maintenance data
 */
class MantenimientoRequest extends FormRequest
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
            'camion_id' => 'required|exists:camiones,id',
            'tipo_mantenimiento' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'fecha_programada' => 'required|date|after_or_equal:today',
            'fecha_realizada' => 'nullable|date|after_or_equal:fecha_programada',
            'costo' => 'nullable|numeric|min:0|max:999999.99',
            'estado' => ['required', Rule::in(Mantenimiento::ESTADOS)],
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
            'camion_id.required' => 'Debe seleccionar un camión',
            'camion_id.exists' => 'El camión seleccionado no existe',
            'tipo_mantenimiento.required' => 'El tipo de mantenimiento es obligatorio',
            'tipo_mantenimiento.max' => 'El tipo de mantenimiento no puede tener más de 255 caracteres',
            'descripcion.max' => 'La descripción no puede tener más de 1000 caracteres',
            'fecha_programada.required' => 'La fecha programada es obligatoria',
            'fecha_programada.date' => 'La fecha programada debe ser una fecha válida',
            'fecha_programada.after_or_equal' => 'La fecha programada no puede ser anterior a hoy',
            'fecha_realizada.date' => 'La fecha realizada debe ser una fecha válida',
            'fecha_realizada.after_or_equal' => 'La fecha realizada debe ser posterior o igual a la fecha programada',
            'costo.numeric' => 'El costo debe ser un número',
            'costo.min' => 'El costo no puede ser negativo',
            'costo.max' => 'El costo no puede ser mayor a Q999,999.99',
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
            'camion_id' => 'camión',
            'tipo_mantenimiento' => 'tipo de mantenimiento',
            'descripcion' => 'descripción',
            'fecha_programada' => 'fecha programada',
            'fecha_realizada' => 'fecha realizada',
            'costo' => 'costo',
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
            // Check if truck has conflicting maintenance on the same date
            if ($this->camion_id && $this->fecha_programada) {
                $conflictingMaintenance = \App\Models\Mantenimiento::where('camion_id', $this->camion_id)
                    ->whereDate('fecha_programada', $this->fecha_programada)
                    ->whereIn('estado', [
                        \App\Models\Mantenimiento::ESTADO_PROGRAMADO,
                        \App\Models\Mantenimiento::ESTADO_EN_PROCESO
                    ])
                    ->when($this->route('mantenimiento'), function ($query) {
                        return $query->where('id', '!=', $this->route('mantenimiento')->id);
                    })
                    ->first();

                if ($conflictingMaintenance) {
                    $validator->errors()->add('fecha_programada', 
                        'El camión ya tiene un mantenimiento programado para esta fecha');
                }
            }

            // Validate that truck is available for maintenance (not on active trip)
            if ($this->camion_id && $this->isMethod('POST')) {
                $camion = \App\Models\Camion::find($this->camion_id);
                if ($camion && $camion->viaje_actual) {
                    $validator->errors()->add('camion_id', 
                        'No se puede programar mantenimiento para un camión con viaje activo');
                }
            }

            // Validate maintenance type
            if ($this->tipo_mantenimiento) {
                // Check if it's a valid maintenance type from the predefined list
                if (!in_array($this->tipo_mantenimiento, \App\Models\Mantenimiento::TIPOS_MANTENIMIENTO)) {
                    // This is not an error, just info for custom types
                    \Log::info('Custom maintenance type used', [
                        'tipo' => $this->tipo_mantenimiento,
                        'user_id' => auth()->id()
                    ]);
                }
            }

            // Validate cost reasonableness
            if ($this->costo && $this->costo > 50000) {
                // This is a high cost, log it for review
                \Log::warning('High cost maintenance scheduled', [
                    'costo' => $this->costo,
                    'tipo' => $this->tipo_mantenimiento,
                    'camion_id' => $this->camion_id,
                    'user_id' => auth()->id()
                ]);
            }

            // Validate that completed maintenance has a completion date
            if ($this->estado === \App\Models\Mantenimiento::ESTADO_COMPLETADO) {
                if (!$this->fecha_realizada) {
                    $validator->errors()->add('fecha_realizada', 
                        'La fecha realizada es obligatoria para mantenimientos completados');
                }
            }

            // Validate that in-process maintenance has a reasonable scheduled date
            if ($this->estado === \App\Models\Mantenimiento::ESTADO_EN_PROCESO) {
                if ($this->fecha_programada && \Carbon\Carbon::parse($this->fecha_programada)->isFuture()) {
                    $validator->errors()->add('estado', 
                        'No se puede marcar como "En Proceso" un mantenimiento programado para el futuro');
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
        // Clean and format maintenance type
        if ($this->tipo_mantenimiento) {
            $this->merge([
                'tipo_mantenimiento' => ucwords(strtolower(trim($this->tipo_mantenimiento)))
            ]);
        }

        // Clean description
        if ($this->descripcion) {
            $this->merge([
                'descripcion' => trim($this->descripcion) ?: null
            ]);
        }

        // Ensure numeric fields are properly formatted
        if ($this->costo) {
            $this->merge([
                'costo' => (float) $this->costo
            ]);
        }

        // Format dates if they are strings
        if ($this->fecha_programada && is_string($this->fecha_programada)) {
            try {
                $fecha = \Carbon\Carbon::parse($this->fecha_programada);
                $this->merge([
                    'fecha_programada' => $fecha->format('Y-m-d')
                ]);
            } catch (\Exception $e) {
                // Leave as is, validation will catch invalid dates
            }
        }

        if ($this->fecha_realizada && is_string($this->fecha_realizada)) {
            try {
                $fecha = \Carbon\Carbon::parse($this->fecha_realizada);
                $this->merge([
                    'fecha_realizada' => $fecha->format('Y-m-d')
                ]);
            } catch (\Exception $e) {
                // Leave as is, validation will catch invalid dates
            }
        }
    }
}