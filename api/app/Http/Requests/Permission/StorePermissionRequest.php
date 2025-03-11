<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->isSuperman();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            "name" => ["required", "unique:permissions", "not_regex:/[^:a-z_-]{1,}/"],
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
