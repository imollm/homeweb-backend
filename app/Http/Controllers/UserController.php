<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * UserController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserUpdateRequest $request
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request): JsonResponse
    {
        $request->validated();

        if ($this->userService->update($request)) {

            return response()->json([
                'success' => true,
                'message' => 'User updated'
            ], Response::HTTP_OK);

        } else {

            return response()->json([
                'success' => false,
                'message' => 'Error while updating user'
            ], Response::HTTP_CONFLICT);

        }
    }

    /**
     * Get all users with role Owner
     *
     * @return JsonResponse
     */
    public function owners(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->userService->getOwners(),
            'message' => 'All owners'
        ], Response::HTTP_OK);
    }
}
