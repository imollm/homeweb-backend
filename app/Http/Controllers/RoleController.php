<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

/**
 * Class RoleController
 * @package App\Http\Controllers
 */
class RoleController extends Controller
{
    /**
     * Return all roles stored on database
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        $roles = Role::all();

        return response()->json([
            'success' => true,
            'data' => $roles,
        ]);
    }

    /**
     * Return role of user id
     *
     * @param string $id
     * @return JsonResponse
     */
    public function userRole(string $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => User::find($id)->role,
        ]);
    }

    public function myRole(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => auth()->user()->role,
        ]);
    }
}
