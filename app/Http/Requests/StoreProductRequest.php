<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta petición.
     */
    public function authorize(): bool
    {
        // true = cualquiera puede usar este request
        // si quieres restringir según roles, cámbialo
        return true;
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:150'],
            'sku'         => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9\-]+$/', 'unique:products,sku'],
            'price'       => ['required', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'expires_at'  => ['nullable', 'date', 'after:today'],
            // Si usas categorías:
            // 'category_id' => ['required', 'exists:categories,id'],
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'name.required'       => 'El nombre es obligatorio.',
            'sku.required'        => 'El SKU es obligatorio.',
            'sku.unique'          => 'Ese SKU ya está registrado.',
            'sku.regex'           => 'El SKU solo acepta letras, números y guiones.',
            'price.min'           => 'El precio no puede ser negativo.',
            'stock.min'           => 'El stock no puede ser negativo.',
            'expires_at.after'    => 'La fecha de expiración debe ser posterior a hoy.',
        ];
    }
}
