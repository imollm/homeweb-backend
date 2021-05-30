<?php

namespace App\Http\Controllers;

use App\Services\File\FileService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

/**
 * Class FileController
 * @package App\Http\Controllers
 */
class FileController extends Controller
{
    /**
     * @var FileService
     */
    private FileService $fileService;

    /**
     * FileController constructor.
     * @param FileService $fileService
     */
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * @OA\Get (
     *     path="/image/categories/{id}",
     *     summary="Get image of category",
     *     tags={"Image"},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of category"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category image.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="string"),
     *             @OA\Property (property="message", type="string", example="Image of category with id {id}"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image of category not found."
     *     ),
     * )
     * @throws FileNotFoundException
     */
    public function categories(string $id): JsonResponse
    {
        if (empty($file = $this->fileService->getCategoryImage($id))) abort(ResponseStatus::HTTP_NOT_FOUND);

        return response()->json([
            'success' => true,
            'data' => base64_encode($file['image']),
            'message' => 'Image of category with id ' . $id
        ], ResponseStatus::HTTP_OK);
    }

    /**
     * @OA\Get (
     *     path="/image/properties/{id}",
     *     summary="Get image of property",
     *     tags={"Image"},
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of property"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Property image.",
     *         @OA\JsonContent (
     *             @OA\Property (property="success", type="boolean", example=true),
     *             @OA\Property (property="data", type="string"),
     *             @OA\Property (property="message", type="string", example="Image of property with id {id}"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image of property not found."
     *     ),
     * )
     * @throws FileNotFoundException
     */
    public function properties(string $id): JsonResponse
    {
        if (empty($file = $this->fileService->getPropertyImage($id))) abort(ResponseStatus::HTTP_NOT_FOUND);

        return response()->json([
            'success' => true,
            'data' => base64_encode($file['image']),
            'message' => 'Image of property with id ' . $id
        ], ResponseStatus::HTTP_OK);
    }
}
