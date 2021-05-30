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
     * @OA\Post(
     * path="/auth/register",
     * summary="Registration",
     * description="User registration",
     * tags={"Authentication"},
     *  @OA\RequestBody(
     *     required=true,
     *     description="User data",
     *     @OA\JsonContent(
     *        required={"name","email","password","phone","address","fiscal_id","role_id"},
     *        @OA\Property(property="name", type="string", example="My name"),
     *        @OA\Property(property="email", type="string", format="email", example="user1@homeweb.com"),
     *        @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *        @OA\Property(property="phone", type="string", example="My phone"),
     *        @OA\Property(property="address", type="string", example="My home address"),
     *        @OA\Property(property="fiscal_id", type="string", example="My fiscal ID"),
     *        @OA\Property(property="role_id", type="integer", example=1),
     *     ),
     *  ),
     *  @OA\Response(
     *     response=201,
     *     description="Logged in",
     *     @OA\JsonContent(
     *       @OA\Property (
     *           property="success", type="boolean", example=true
     *       ),
     *           @OA\Property (
     *               property="dataUser", type="object",
     *               @OA\Property (property="id", type="integer", example=1),
     *               @OA\Property (property="name", type="string", example="User 1"),
     *               @OA\Property (property="email", type="string", example="user1@homeweb.com"),
     *               @OA\Property (property="accessToken", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZTRjMWEzNzE5YTc5ZTU3YWNlYjBlOWJmYWY2M2ZlNjU1NDI5ODdmYWIzZDdkNWMyZTJkZTUzNDUxMDQ2OGJmZmIzNmEwNjYwYmY2ZTY2OWYiLCJpYXQiOiIxNjIyMTM2MTg0Ljg4MzgyMiIsIm5iZiI6IjE2MjIxMzYxODQuODgzODM0IiwiZXhwIjoiMTY1MzY3MjE4NC44NzQ5NjQiLCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.IjweTMVW2hDVOzGfIfoiMWYNGTnAQiiyyOCGvVr81z-UC5UVHIqdSmrgAY9H3IUpZbmLZQqgvzlAN6q0RMOSaVy35tc-aZwmNv8sIWRGOZriISRdQws4QW7fhpEQwY4GGY73vB0ISB8U7lPE0-qOhMO_S88JTsKf9DIqFnMdfFjFlhWBfPEj_CL81cuHWjGCzr-5OmiAMAyb_RgXh9X7HEOHqxEj0YHEa5hJapEWcHt7IT3ii4IFBqp59egWbRnzmx1bJGXA2tHaPQLohLoki6G0K9bxXF0C4V-AgHcTI7B9QFao-V6vEiBIlgkAoH368QQMxu2ePVXg83bfnbRe0WcIMqchGjtwIRLhzR3ZlUSpKKzwLIVhdEbWK9bWFmgqZXyojWBrOGJBFcYUJ0dZ0r1-9YLPfRPt4WiYOYLGgWqvRBNKGHopub5fx2p7lYx34_vcXH-iJkd2TIwCcVexjDs-dlGTh45xEby5LPaONwQzt6ZmYHPXb3NlQ1p9F96Fsjl11IPVWp45HRCAcsdUOJ_nosjW-COVsMzU4eAlgWo0MjKwAYyRBwvCkog2B3NIRWmQZSH49FOC3XjbXh_AFBz0FBklifKf9GsiXwOTXPRMjJCUUVubGofZ_Q1_qA1s6ENBWca16V3ThC-eyCI2tPZpmPnNOrLXyRFeaieJtJk"),
     *               @OA\Property (property="role", type="string", example="admin"),
     *           ),
     *     ),
     *  ),
     *  @OA\Response(
     *     response=401,
     *     description="Unregistred user",
     *     @OA\JsonContent (
     *         @OA\Property (property="success", type="boolean", example=false),
     *         @OA\Property (property="message", type="string", example="Unauthorized user"),
     *     ),
     *  ),
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $this->passportAuthService->validateNewUserData($request);

        $user = $this->passportAuthService->registerNewUser($request);

        return response()->json([
            'success' => true,
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
     * @OA\Post(
     * path="/auth/login",
     * summary="Sign in",
     * description="Login by email, password",
     * tags={"Authentication"},
     *  @OA\RequestBody(
     *     required=true,
     *     description="Pass user credentials",
     *     @OA\JsonContent(
     *        required={"email","password"},
     *        @OA\Property(property="email", type="string", format="email", example="user1@homeweb.com"),
     *        @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *     ),
     *  ),
     *  @OA\Response(
     *     response=200,
     *     description="Logged in",
     *     @OA\JsonContent(
     *       @OA\Property (
     *           property="success", type="boolean", example=true
     *       ),
     *           @OA\Property (
     *               property="dataUser", type="object",
     *               @OA\Property (property="id", type="integer", example=1),
     *               @OA\Property (property="name", type="string", example="User 1"),
     *               @OA\Property (property="email", type="string", example="user1@homeweb.com"),
     *               @OA\Property (property="accessToken", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZTRjMWEzNzE5YTc5ZTU3YWNlYjBlOWJmYWY2M2ZlNjU1NDI5ODdmYWIzZDdkNWMyZTJkZTUzNDUxMDQ2OGJmZmIzNmEwNjYwYmY2ZTY2OWYiLCJpYXQiOiIxNjIyMTM2MTg0Ljg4MzgyMiIsIm5iZiI6IjE2MjIxMzYxODQuODgzODM0IiwiZXhwIjoiMTY1MzY3MjE4NC44NzQ5NjQiLCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.IjweTMVW2hDVOzGfIfoiMWYNGTnAQiiyyOCGvVr81z-UC5UVHIqdSmrgAY9H3IUpZbmLZQqgvzlAN6q0RMOSaVy35tc-aZwmNv8sIWRGOZriISRdQws4QW7fhpEQwY4GGY73vB0ISB8U7lPE0-qOhMO_S88JTsKf9DIqFnMdfFjFlhWBfPEj_CL81cuHWjGCzr-5OmiAMAyb_RgXh9X7HEOHqxEj0YHEa5hJapEWcHt7IT3ii4IFBqp59egWbRnzmx1bJGXA2tHaPQLohLoki6G0K9bxXF0C4V-AgHcTI7B9QFao-V6vEiBIlgkAoH368QQMxu2ePVXg83bfnbRe0WcIMqchGjtwIRLhzR3ZlUSpKKzwLIVhdEbWK9bWFmgqZXyojWBrOGJBFcYUJ0dZ0r1-9YLPfRPt4WiYOYLGgWqvRBNKGHopub5fx2p7lYx34_vcXH-iJkd2TIwCcVexjDs-dlGTh45xEby5LPaONwQzt6ZmYHPXb3NlQ1p9F96Fsjl11IPVWp45HRCAcsdUOJ_nosjW-COVsMzU4eAlgWo0MjKwAYyRBwvCkog2B3NIRWmQZSH49FOC3XjbXh_AFBz0FBklifKf9GsiXwOTXPRMjJCUUVubGofZ_Q1_qA1s6ENBWca16V3ThC-eyCI2tPZpmPnNOrLXyRFeaieJtJk"),
     *               @OA\Property (property="role", type="string", example="admin"),
     *           ),
     *     ),
     *  ),
     *  @OA\Response(
     *     response=401,
     *     description="Unregistred user",
     *     @OA\JsonContent (
     *         @OA\Property (property="success", type="boolean", example=false),
     *         @OA\Property (property="message", type="string", example="Unauthorized user"),
     *     ),
     *  ),
     * )
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
                'success' => true,
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
     * @OA\Get (
     * path="/auth/logout",
     * summary="Sign out",
     * description="User logout",
     * tags={"Authentication"},
     * security={{ "apiAuth": {} }},
     *  @OA\Response(
     *     response=200,
     *     description="Logged out",
     *     @OA\JsonContent(
     *       @OA\Property (property="success", type="boolean", example=true),
     *       @OA\Property (property="dataUser", type="object"),
     *     ),
     *  ),
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return response()->json([
            'success' => true,
            'dataUser' => []
        ],Response::HTTP_OK);
    }

    /**
     * @OA\Get (
     * path="/auth/user",
     * summary="User info",
     * description="Get user info",
     * tags={"Authentication"},
     * security={{ "apiAuth": {} }},
     *  @OA\Response(
     *     response=200,
     *     description="Logged in",
     *     @OA\JsonContent(
     *       @OA\Property (
     *           property="success", type="boolean", example=true
     *       ),
     *           @OA\Property (
     *               property="data", type="object",
     *               @OA\Property (property="id", type="integer", example=1),
     *               @OA\Property (property="name", type="string", example="User 1"),
     *               @OA\Property (property="email", type="string", example="user1@homeweb.com"),
     *               @OA\Property (property="email_verified_at", type="string", example="null"),
     *               @OA\Property(property="phone", type="string", example="My phone"),
     *               @OA\Property(property="address", type="string", example="My home address"),
     *               @OA\Property(property="fiscal_id", type="string", example="My fiscal ID"),
     *               @OA\Property(property="role_id", type="integer", example=1),
     *               @OA\Property (property="created_at", type="string", example="2021-05-05T20:10:21.000000Z"),
     *               @OA\Property (property="updated_at", type="string", example="2021-05-05T20:10:21.000000Z"),
     *               @OA\Property (
     *                  property="role", type="object",
     *                  @OA\Property (property="id", type="integer", example=1),
     *                  @OA\Property (property="name", type="string", example="admin"),
     *                  @OA\Property (property="description", type="string", example="Administrador"),
     *                  @OA\Property (property="created_at", type="string", example="2021-05-05T20:10:21.000000Z"),
     *                  @OA\Property (property="updated_at", type="string", example="2021-05-05T20:10:21.000000Z"),
     *              ),
     *           ),
     *       @OA\Property (property="message", type="string", example="Auth user"),
     *     ),
     *  ),
     * )
     */
    public function user(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->passportAuthService->authUser(),
            'message' => 'Auth user'
        ], Response::HTTP_OK);
    }
}
