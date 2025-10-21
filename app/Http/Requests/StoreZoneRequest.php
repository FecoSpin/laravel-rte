<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin') || $this->user()->hasRole('supervisor');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:zones',
            'description' => 'nullable|string|max:1000',
            'active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la zona es obligatorio',
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'code.required' => 'El código de la zona es obligatorio',
            'code.unique' => 'Este código ya está en uso',
            'code.max' => 'El código no puede exceder 50 caracteres',
            'description.max' => 'La descripción no puede exceder 1000 caracteres',
        ];
    }
}
