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
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $features = $this->featureService->getAllFeatures();

        if (count($features) <= 0) {

            return response()->json([
                'success' => true,
                'message' => 'No features in system'
            ], Response::HTTP_OK);

        } else {

            return response()->json([
                'success' => true,
                'data' => $features,
                'message' => 'All features'
            ], Response::HTTP_OK);

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Display the specified resource.
     *
     * @param string $id
     * @return JsonResponse
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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
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
