<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RoleController
 * @package App\Http\Controllers
 */
class RoleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/roles/all",
     *     summary="Get all roles",
     *     tags={"Roles"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="All categories.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="All roles of system"),
     *         ),
     *     )
     * )
     */
    public function all(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' =>  Role::all(),
            'message' => 'All roles of system'
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/roles/{id}",
     *     summary="Get role by user id",
     *     tags={"Roles"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of user"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role of user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="ARole of user by id X"),
     *         ),
     *     )
     * )
     */
    public function userRole(string $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => User::find($id)->role,
            'message' => 'Role of user by id ' . $id
        ]);
    }

    /**
     * @OA\Get(
     *     path="/roles/myRole",
     *     summary="Get my role",
     *     tags={"Roles"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Get your role.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Your role"),
     *         ),
     *     )
     * )
     */
    public function myRole(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => auth()->user()->role,
            'message' => 'Your role'
        ]);
    }
}
