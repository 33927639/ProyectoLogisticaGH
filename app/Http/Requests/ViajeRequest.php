<?php

namespace App\Http\Requests;

use App\Models\Viaje;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ViajeRequest
 *
 * Form request for validating trip data
 */
class ViajeRequest extends FormRequest
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
            'piloto_id' => 'required|exists:pilotos,id',
            'ruta_id' => 'required|exists:rutas,id',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'kilometraje_inicial' => 'nullable|numeric|min:0',
            'kilometraje_final' => 'nullable|numeric',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'estado' => ['nullable', Rule::in(Viaje::ESTADOS)],
        ];

        // Add conditional validation for kilometraje_final
        if ($this->kilometraje_inicial) {
            $rules['kilometraje_final'] = 'nullable|numeric|gte:kilometraje_inicial';
        }

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
            'piloto_id.required' => 'Debe seleccionar un piloto',
            'piloto_id.exists' => 'El piloto seleccionado no existe',
            'ruta_id.required' => 'Debe seleccionar una ruta',
            'ruta_id.exists' => 'La ruta seleccionada no existe',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida',
            'fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy',
            'kilometraje_inicial.numeric' => 'El kilometraje inicial debe ser un número',
            'kilometraje_inicial.min' => 'El kilometraje inicial no puede ser negativo',
            'kilometraje_final.numeric' => 'El kilometraje final debe ser un número',
            'kilometraje_final.gte' => 'El kilometraje final debe ser mayor o igual al inicial',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
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
            'piloto_id' => 'piloto',
            'ruta_id' => 'ruta',
            'fecha_inicio' => 'fecha de inicio',
            'kilometraje_inicial' => 'kilometraje inicial',
            'kilometraje_final' => 'kilometraje final',
            'fecha_fin' => 'fecha de fin',
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
            // Custom validation to check resource availability
            if ($this->camion_id && $this->piloto_id && $this->ruta_id) {
                $camion = \App\Models\Camion::find($this->camion_id);
                $piloto = \App\Models\Piloto::find($this->piloto_id);
                $ruta = \App\Models\Ruta::find($this->ruta_id);

                // Skip availability check when updating existing trip
                if ($this->isMethod('POST')) {
                    if ($camion && !$camion->estaDisponible()) {
                        $validator->errors()->add('camion_id', 'El camión seleccionado no está disponible');
                    }

                    if ($piloto && !$piloto->esta_disponible) {
                        $validator->errors()->add('piloto_id', 'El piloto seleccionado no está disponible');
                    }
                }

                if ($ruta && $ruta->estado !== \App\Models\Ruta::ESTADO_ACTIVA) {
                    $validator->errors()->add('ruta_id', 'La ruta seleccionada no está activa');
                }

                // Check for scheduling conflicts
                if ($this->fecha_inicio && $this->camion_id) {
                    $conflictingTrip = \App\Models\Viaje::where('camion_id', $this->camion_id)
                        ->where('estado', \App\Models\Viaje::ESTADO_PROGRAMADO)
                        ->whereDate('fecha_inicio', $this->fecha_inicio)
                        ->when($this->route('viaje'), function ($query) {
                            return $query->where('id', '!=', $this->route('viaje')->id);
                        })
                        ->first();

                    if ($conflictingTrip) {
                        $validator->errors()->add('fecha_inicio', 'El camión ya tiene un viaje programado para esta fecha');
                    }
                }

                if ($this->fecha_inicio && $this->piloto_id) {
                    $conflictingTrip = \App\Models\Viaje::where('piloto_id', $this->piloto_id)
                        ->where('estado', \App\Models\Viaje::ESTADO_PROGRAMADO)
                        ->whereDate('fecha_inicio', $this->fecha_inicio)
                        ->when($this->route('viaje'), function ($query) {
                            return $query->where('id', '!=', $this->route('viaje')->id);
                        })
                        ->first();

                    if ($conflictingTrip) {
                        $validator->errors()->add('fecha_inicio', 'El piloto ya tiene un viaje programado para esta fecha');
                    }
                }
            }
        });
    }
}