<?php

namespace App\Http\Requests\Record;

use Illuminate\Foundation\Http\FormRequest;

class CreateRecordRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'document_type_id' => 'required|exists:document_types,id',
            'user_id' => 'required|exists:users,id',
<<<<<<< Updated upstream
=======
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'document_type.id.exists' => 'The selected document type is invalid.',
            'user_id.exists' => 'The selected user is invalid.',
            'files.*.mimes' => 'The file must be a file of type: pdf, jpg, jpeg, png.',
            'files.*.max' => 'The file may not be greater than 2048 kilobytes.',
>>>>>>> Stashed changes
        ];
    } 
}
