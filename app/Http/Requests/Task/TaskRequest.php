<?php

namespace App\Http\Requests\Task;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class TaskRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'task_group_id' => 'integer|required',
            'title' => 'string|required',
            'breifing' => 'string|required',
            'details' => 'string|nullable',
            'reminder' => 'date',
            'filters.user_id' => 'nullable|integer|exists:users,id',
            'filters.phase_id' => 'nullable|string|exists:task_phases,id',
            'filters.task_group_id' => 'nullable|integer|exists:task_group_id,id',
        ];
    }


     /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
        'errors' => $validator->errors(),
        'status' => false
        ], 422));
    }
}
