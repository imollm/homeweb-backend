<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Services\Feature\FeatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FeatureController
 * @package App\Http\Controllers
 */
class FeatureController extends Controller
{
    /**
     * @var FeatureService
     */
    private FeatureService $featureService;

    /**
     * FeatureController constructor.
     * @param FeatureService $featureService
     */
    public function __construct(FeatureService $featureService)
    {
        $this->featureService = $featureService;
    }

    /**
     * @OA\Get(
     *     path="/features/index",
     *     summary="Get all features",
     *     tags={"Features"},
     *     @OA\Response(
     *         response=200,
     *         description="All features.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="All features"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No features in system.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="No features in system"),
     *         ),
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $features = $this->featureService->getAllFeatures();

        if (count($features) <= 0) {

            return response()->json([
                'success' => true,
                'message' => 'No features in system'
            ], Response::HTTP_NOT_FOUND);

        } else {

            return response()->json([
                'success' => true,
                'data' => $features,
                'message' => 'All features'
            ], Response::HTTP_OK);

        }
    }

    /**
     * @OA\Post(
     *     path="/features/create",
     *     summary="Store new feature",
     *     tags={"Features"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Feature data",
     *          @OA\JsonContent(
     *             @OA\Property(property="name", description="Name of feature", type="string", example="Foo")
     *          ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Feature created.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Feature created"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while creating feature.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error while creating feature"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Unauthorized User"),
     *         ),
     *     )
     * )
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        if (Auth::user()->can('store', Feature::class)) {

            $this->featureService->validatePostData($request);

            if ($this->featureService->create($request)) {

                return response()->json([
                    'success' => true,
                    'message' => 'Feature created'
                ], Response::HTTP_CREATED);

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Error while creating feature'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);

            }

        } else {

            return $this->unauthorizedUser();

        }
    }

    /**
     * @OA\Get(
     *     path="/features/{id}/show",
     *     summary="Get feature by id",
     *     tags={"Features"},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of feature"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Feature by id.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="object"),
     *             @OA\Property (property="message", type="string", example="Feature by id {id}"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Feature not found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Feature not found"),
     *         ),
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        if (is_array($feature = $this->featureService->getFeatureById($id))) {

            return response()->json([
                'success' => true,
                'data' => $feature,
                'message' => 'Feature by id ' . $id
            ], Response::HTTP_OK);

        } else {

            return response()->json([
                'success' => false,
                'message' => 'Feature not found'
            ], Response::HTTP_NOT_FOUND);

        }
    }

    /**
     * @OA\Put(
     *     path="/features/update",
     *     summary="Update feature",
     *     tags={"Features"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Feature data",
     *          @OA\JsonContent(
     *             @OA\Property(property="name", description="Name of feature", type="string", example="Foo")
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Feature updated.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Feature updated"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while updating feature.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error while updating feature"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Feature not found.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Feature not found"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid put data."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Unauthorized User"),
     *         ),
     *     )
     * )
     * @throws ValidationException
     */
    public function update(Request $request): JsonResponse
    {
        if (Auth::user()->can('update', Feature::class)) {

            $this->featureService->validatePostData($request);

            if ($this->featureService->existsThisFeature($request)) {

                if ($this->featureService->update($request)) {

                    return response()->json([
                        'success' => true,
                        'message' => 'Feature updated'
                    ], Response::HTTP_OK);

                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'Error while updating feature'
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);

                }

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Feature not found'
                ], Response::HTTP_NOT_FOUND);

            }

        } else {

            return $this->unauthorizedUser();

        }
    }

    /**
     * @OA\Delete(
     *     path="/features/{id}/delete",
     *     summary="Delete feature by id",
     *     tags={"Features"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of feature"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Feature deleted.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="message", type="string", example="Feature deleted"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while deleting feature.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Error while deleting feature"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="This feature can not be deleted.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="This feature can not be deleted"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized user.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=false),
     *             @OA\Property (property="message", type="string", example="Unauthorized User"),
     *         ),
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        if (Auth::user()->can('destroy', Feature::class)) {

            if (is_array($feature = $this->featureService->getFeatureById($id)) &&
            $this->featureService->canThisFeatureBeDeleted($id)) {

                if ($this->featureService->delete($id)) {

                    return response()->json([
                        'success' => true,
                        'message' => 'Feature deleted'
                    ], Response::HTTP_OK);

                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'Error while deleting feature'
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);

                }

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'This feature can not be deleted'
                ], Response::HTTP_CONFLICT);

            }

        } else {

            return $this->unauthorizedUser();

        }
    }
}
