<?php

namespace App\Http\Controllers;

use App\Services\Auth\PassportAuthService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PassportAuthController
 * @package App\Http\Controllers
 */
class PassportAuthController extends Controller
{
    /**
     * @var PassportAuthService
     */
    private PassportAuthService $passportAuthService;

    /**
     * PassportAuthController constructor.
     *
     * @param PassportAuthService $passportAuthService
     */
    public function __construct(PassportAuthService $passportAuthService)
    {
        $this->passportAuthService = $passportAuthService;
    }

    /**
     * Registration
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $this->passportAuthService->validateNewUserData($request);

        $user = $this->passportAuthService->registerNewUser($request);

        return response()->json([
            'dataUser' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'accessToken' => $user->token,
                'role' => $user->role->name,
            ]
        ], Response::HTTP_CREATED);
    }

    /**
     * Login
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        $user = $this->passportAuthService->login($credentials);

        if ($user && is_object($user)) {
            return response()->json([
                'dataUser' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'accessToken' => $user->token,
                    'role' => $user->role,
                ]
            ], Response::HTTP_OK);
        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * Logout
     *
     * @param Request $request
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function logout(Request $request): Application|ResponseFactory|\Illuminate\Http\Response
    {

        $request->user()->token()->revoke();

        return response('',Response::HTTP_NO_CONTENT);
    }

    /**
     * Get user object as JSON
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse {

        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'message' => 'Auth user'
        ], Response::HTTP_OK);
    }
}
