<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Class PropertyController
 * @package App\Http\Controllers
 */
class PropertyController extends Controller
{
    /**
     * Return all models stored in database.
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        if (Auth::user()->can('all', Property::class)) {

            $properties = Property::all();

            return response()->json([
                'success' => true,
                'data' => $properties,
                'message' => 'List of all properties',
            ]);
        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * Show a property by id
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        if (Auth::user()->can('show', $property)) {

            return response()->json([
                'success' => true,
                'data' => $property,
                'message' => 'The property was request'
            ]);

        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * Create a property model into database
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        if (Auth::user()->can('create', Property::class)) {

            $property = new Property();

            $this->validate($request, [
                'reference' => 'required|string|unique:properties|max:255',
                'plot_meters' => 'required|numeric',
                'built_meters' => 'required|numeric',
                'address' => 'required|string|max:255',
                'longitude' => 'required|numeric',
                'latitude' => 'required|numeric',
                'description' => 'string|max:255',
                'energetic_certification' => ['required', Rule::in(['obtained', 'in progress', 'pending'])],
            ]);

            $property->reference = $request->input('reference');
            $property->plot_meters = $request->input('plot_meters');
            $property->built_meters = $request->input('built_meters');
            $property->address = $request->input('address');
            $property->location = json_encode(["longitude" => (float)$request->input('longitude'), "latitude" => (float)$request->input('latitude')], JSON_FORCE_OBJECT);
            $property->description = $request->input('description');
            $property->energetic_certification = $request->input('energetic_certification');

            if (auth()->user()->properties()->save($property))
                return response()->json([
                    'success' => true,
                    'data' => $property->toArray(),
                    'message' => 'Property was added correctly',
                ]);
            else
                return response()->json([
                    'success' => false,
                    'message' => 'Property not added',
                ], 500);
        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * Update auth user property
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        if (Auth::user()->can('update', $property)) {

            $this->validate($request, [
                'plot_meters' => 'required|numeric',
                'built_meters' => 'required|numeric',
                'address' => 'required|string|max:255',
                'longitude' => 'required|numeric',
                'latitude' => 'required|numeric',
                'description' => 'string|max:255',
                'energetic_certification' => ['required', Rule::in(['obtained', 'in progress', 'pending'])],
            ]);

            $updated = Property::find($id)->update(
                [
                    'plot_meters' => $request->input('plot_meters'),
                    'built_meters' => $request->input('built_meters'),
                    'address' => $request->input('address'),
                    'location' => json_encode(["longitude" => (float)$request->input('longitude'), "latitude" => (float)$request->input('latitude')], JSON_FORCE_OBJECT),
                    'description' => $request->input('description'),
                    'energetic_certification' => $request->input('energetic_certification'),
                ]
            );

            if ($updated)
                return response()->json([
                    'success' => true,
                    'data' => Property::find($id),
                    'message' => 'Property updated successfully',
                ], 201);
            else
                return response()->json([
                    'success' => false,
                    'message' => 'Property can not be updated'
                ], 500);
        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * Set active field to display property on public
     *
     * @param string $id
     * @param string $status
     * @return JsonResponse
     */
    public function setActive(string $id, string $status): JsonResponse
    {
        $property = Property::find($id);

        if(!$property)
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);

        if (Auth::user()->can('setActive', $property)) {

            $property->active = (bool)$status;
            $property->save();

            return response()->json([
                'success' => true,
                'message' => '',
            ], 204);

        } else {
            return $this->unauthorizedUser();
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $post = auth()->user()->properties()->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 400);
        }

        if ($post->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Post can not be deleted'
            ], 500);
        }
    }

    /**
     * To get owner
     *
     * @param string $id
     * @return JsonResponse
     */
    public function owner(string $id): JsonResponse
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property can not be retrieve',
            ]);
        } else {
            $owner = $property->owner();

            if (!$owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner can not be retrieve',
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => $owner,
                ]);
            }
        }
    }
}
