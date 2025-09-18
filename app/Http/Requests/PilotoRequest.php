<?php

namespace App\Http\Requests;

use App\Models\Piloto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class PilotoRequest
 *
 * Form request for validating driver data
 */
class PilotoRequest extends FormRequest
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
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'licencia' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pilotos', 'licencia')->ignore($this->piloto)
            ],
            'telefono' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('pilotos', 'email')->ignore($this->piloto)
            ],
            'estado' => ['required', Rule::in(Piloto::ESTADOS)],
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
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres',
            'apellido.required' => 'El apellido es obligatorio',
            'apellido.max' => 'El apellido no puede tener más de 255 caracteres',
            'licencia.required' => 'El número de licencia es obligatorio',
            'licencia.unique' => 'Ya existe un piloto con este número de licencia',
            'licencia.max' => 'El número de licencia no puede tener más de 50 caracteres',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres',
            'email.email' => 'El correo electrónico debe tener un formato válido',
            'email.unique' => 'Ya existe un piloto con este correo electrónico',
            'email.max' => 'El correo electrónico no puede tener más de 255 caracteres',
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
            'nombre' => 'nombre',
            'apellido' => 'apellido',
            'licencia' => 'número de licencia',
            'telefono' => 'teléfono',
            'email' => 'correo electrónico',
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
            // Validate license format (assuming Guatemalan format)
            if ($this->licencia) {
                // Basic validation for Guatemalan license format
                if (!preg_match('/^[A-Z0-9\-\s]+$/i', $this->licencia)) {
                    $validator->errors()->add('licencia', 'El formato de la licencia no es válido');
                }
            }

            // Validate phone format
            if ($this->telefono) {
                // Remove all non-numeric characters for validation
                $cleanPhone = preg_replace('/[^0-9]/', '', $this->telefono);
                
                // Check if it's a valid Guatemalan phone number (8 digits)
                if (strlen($cleanPhone) < 8 || strlen($cleanPhone) > 8) {
                    $validator->errors()->add('telefono', 'El teléfono debe tener 8 dígitos');
                }
            }

            // Validate name format
            if ($this->nombre) {
                if (!preg_match('/^[a-zA-ZáéíóúñÑ\s]+$/u', $this->nombre)) {
                    $validator->errors()->add('nombre', 'El nombre solo puede contener letras y espacios');
                }
            }

            if ($this->apellido) {
                if (!preg_match('/^[a-zA-ZáéíóúñÑ\s]+$/u', $this->apellido)) {
                    $validator->errors()->add('apellido', 'El apellido solo puede contener letras y espacios');
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
        // Clean and format name and surname
        if ($this->nombre) {
            $this->merge([
                'nombre' => ucwords(strtolower(trim($this->nombre)))
            ]);
        }

        if ($this->apellido) {
            $this->merge([
                'apellido' => ucwords(strtolower(trim($this->apellido)))
            ]);
        }

        // Clean and format license
        if ($this->licencia) {
            $this->merge([
                'licencia' => strtoupper(trim($this->licencia))
            ]);
        }

        // Clean and format email
        if ($this->email) {
            $this->merge([
                'email' => strtolower(trim($this->email))
            ]);
        }

        // Clean phone number
        if ($this->telefono) {
            // Keep only numbers, hyphens, and spaces for display
            $cleanPhone = preg_replace('/[^0-9\-\s]/', '', $this->telefono);
            $this->merge([
                'telefono' => $cleanPhone ?: null
            ]);
        }
    }
}