<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormularioInformeRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'usuario_id' => 'required|integer|exists:users,id',
            'fecha_creacion' => 'required|date',
            'campos_formativos' => 'required|array|min:1',
            'campos_formativos.*.nombre' => 'required|string|max:255',
            'campos_formativos.*.grados' => 'required|array|size:6',
            'campos_formativos.*.grados.*.grado' => 'required|integer|min:1|max:6',
            'campos_formativos.*.grados.*.cantidad_alumnos' => 'required|integer|min:0',
            'participa_en_proyectos' => 'required|boolean',
            'proyectos_colaborativos' => 'required_if:participa_en_proyectos,true|array',
            'proyectos_colaborativos.*' => 'string|max:255',
            'se_inscribio_en_cursos' => 'required|boolean',
            'cantidad_cursos' => 'required_if:se_inscribio_en_cursos,true|integer|min:0',
            'cursos_en_linea' => 'required_if:se_inscribio_en_cursos,true|array',
            'cursos_en_linea.*' => 'string|max:255',
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
            if ($this->se_inscribio_en_cursos && 
                $this->cantidad_cursos !== count($this->cursos_en_linea ?? [])) {
                $validator->errors()->add(
                    'cursos_en_linea', 
                    'La cantidad de cursos debe coincidir con el número de cursos proporcionados.'
                );
            }

            if ($this->participa_en_proyectos && empty($this->proyectos_colaborativos)) {
                $validator->errors()->add(
                    'proyectos_colaborativos', 
                    'Debe proporcionar al menos un proyecto colaborativo.'
                );
            }

            if ($this->se_inscribio_en_cursos && $this->cantidad_cursos < 1) {
                $validator->errors()->add(
                    'cantidad_cursos', 
                    'Debe inscribirse al menos en un curso.'
                );
            }

            // Validar que al menos un grado tenga alumnos en algún campo formativo
            if ($this->campos_formativos) {
                $hasStudents = false;
                foreach ($this->campos_formativos as $campo) {
                    foreach ($campo['grados'] as $grado) {
                        if ($grado['cantidad_alumnos'] > 0) {
                            $hasStudents = true;
                            break 2;
                        }
                    }
                }
                
                if (!$hasStudents) {
                    $validator->errors()->add(
                        'campos_formativos', 
                        'Al menos un grado debe tener alumnos.'
                    );
                }
            }
        });
    }
}
