<?php

namespace App\Http\Controllers;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class PassportAuthController extends Controller
{
    /**
     * Registration
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:8',
            'phone' => 'required|unique:users|min:9',
            'address' => 'required|max:255',
            'fiscal_id' => 'required|unique:users|max:25',
            'role_id' => 'required|numeric'
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'fiscal_id' => $request->input('fiscal_id'),
            'role_id' => $request->input('role_id'),
        ]);

        $token = $user->createToken('LaravelAuthApp')->accessToken;

        $roles = User::find($user->id)->role;

        return response()->json([
            'dataUser' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'accessToken' => $token,
                'roles' => $roles,
            ]
        ], 200);
    }

    /**
     * Login
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $data = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            $user = auth()->user();
            $roles = User::find(auth()->id())->role;
            return response()->json([
                'dataUser' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'accessToken' => $token,
                    'roles' => $roles,
                ]
            ], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
}
