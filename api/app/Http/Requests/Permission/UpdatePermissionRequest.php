<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        // return $this->user()->isSuperman();
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            "name" => [
                "required",
                "unique:permissions,name,{$this->permission->id}",
                "not_regex:/[^:a-z_-]{1,}/",
            ],
            "description" => "nullable|string",
        ];
    }

    public function messages(): array {
        return [
            "name.required" => "The permission name is required",
            "name.unique" => "The permission name already exists",
            "name.not_regex" => "The permission name is invalid",
        ];
    }
}
