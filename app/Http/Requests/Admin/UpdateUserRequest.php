<?php
namespace App\Http\Requests\Admin;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->role === 'admin'; }

    public function rules(): array {
        $userId = $this->route('user')->id_user;
        return [
            'nama' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId, 'id_user')],
            'role' => 'required|in:admin,operator,penumpang',
           'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }
}
