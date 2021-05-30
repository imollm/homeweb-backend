<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Homeweb API Documentation",
     *      description="Homeweb API endpoints",
     *      @OA\Contact(
     *          email="imollm@uoc.edu"
     *      ),
     *      @OA\License(
     *          name="MIT License",
     *          url="https://opensource.org/licenses/MIT"
     *      )
     * )
     *   @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Localhost API Server"
     * )
     *   @OA\Server(
     *      url=L5_SWAGGER_CONST_DOCKER_HOST,
     *      description="Docker Host API Server"
     * )
     *
     * @OA\SecurityScheme(
     *     type="http",
     *     description="Use first Authentication -> /auth/login endpoint, with email and password and get JWT to put into this field",
     *     name="Token based Based",
     *     in="header",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     securityScheme="apiAuth",
     * )
     */

    public function unauthorizedUser(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized User',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
