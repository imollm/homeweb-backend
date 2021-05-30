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
     * @OA\Put (
     *     path="/users/update",
     *     summary="Update user info",
     *     tags={"Users"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="User updated.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="User updated"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Error while updating user info.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error while updating user"),
     *         ),
     *     )
     * )
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
     * @OA\Get (
     *     path="/users/owners",
     *     summary="Get owners of system",
     *     tags={"Users"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="All owners.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="All owners"),
     *         ),
     *     )
     * )
     */
    public function owners(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->userService->getOwners(),
            'message' => 'All owners'
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get (
     *     path="/users/customers",
     *     summary="Get customers of system",
     *     tags={"Users"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="All customers.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="All customers"),
     *         ),
     *     )
     * )
     */
    public function customers(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->userService->getCustomers(),
            'message' => 'All customers'
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get (
     *     path="/users/employees",
     *     summary="Get employees of system",
     *     tags={"Users"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="All employees.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="All employees"),
     *         ),
     *     )
     * )
     */
    public function employees(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->userService->getEmployees(),
            'message' => 'All employees'
        ], Response::HTTP_OK);
    }
}
