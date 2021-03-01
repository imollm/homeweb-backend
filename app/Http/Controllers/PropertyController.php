<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class PropertyController
 * @package App\Http\Controllers
 */
class PropertyController extends Controller
{
    /**
     * Return all models stored in database.
     * Role Admin, Employee return all properties.
     * Role Owner return own properties.
     * Role Customer return interested properties.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $role = auth()->user()->role->first()->name;
        $message = 'The properties that you can view';
        $success = true;
        $properties = '';

        switch ($role) {
            case 'admin':
            case 'employee':
                // All properties
                $properties = Property::all();
                break;
            case 'owner':
                // Own properties
                $properties = auth()->user()->properties;
                break;
            case 'customer':
                // Interested properties
            default:
                $success = false;
                $message = 'No properties found for you';
        }
        return response()->json([
            'success' => $success,
            'data' => $properties,
            'message' => $message,
            'role' => $role,
        ]);
    }

    /**
     * Show a property of auth user
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $property = auth()->user()->properties()->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $property->toArray()
        ], 400);
    }

    /**
     * Create a property model into database
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        auth()->user()->authorizeRoles(['admin', 'owner', 'employee']);

        $this->validate($request, [
            'reference' => 'required|string|unique:properties|max:255',
            'plot_meters' => 'required|numeric',
            'address' => 'required|string|max:255',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'description' => 'string|max:255'
        ]);

        $property = new Property();
        $property->reference = $request->input('reference');
        $property->plot_meters = $request->input('plot_meters');
        $property->address = $request->input('address');
        $property->location = json_encode(["longitude" => (float)$request->input('longitude'), "latitude" => (float)$request->input('latitude')], JSON_FORCE_OBJECT);
        $property->description = $request->input('description');

        if (auth()->user()->properties()->save($property))
            return response()->json([
                'success' => true,
                'data' => $property->toArray()
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Post not added'
            ], 500);
    }

    /**
     * Update auth user property
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $property = auth()->user()->properties()->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 400);
        }

        $updated = $property->fill($request->all())->save();

        if ($updated)
            return response()->json([
                'success' => true
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Post can not be updated'
            ], 500);
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
