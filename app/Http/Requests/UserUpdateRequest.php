<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param UserService $userService
     * @return bool
     */
    public function authorize(UserService $userService): bool
    {
        $user = $userService->getUserById($this->input('id'));

        return $user && Auth::user()->can('update', $user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|max:255',
        ];
    }

    public function response(array $errors): JsonResponse
    {
        return response()->json($errors, 422);
    }
}
