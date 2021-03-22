<?php


namespace App\Services\Feature;


use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class FeatureService
 * @package App\Services\Feature
 */
class FeatureService implements IFeatureService
{
    /**
     * @return array
     */
    public function getAllFeatures(): array
    {
        return Feature::all()->toArray();
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    public function validatePostData(Request $request)
    {
        Validator::make($request->all(), [
            'id' => 'nullable|numeric',
            'name' => 'required|unique:features|string|max:255',
        ])->validate();
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function create(Request $request): bool
    {
        $feature = Feature::create([
            'name' => $request->input('name')
        ]);

        return !is_null($feature);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function existsThisFeature(Request $request): bool
    {
        $featureId = $request->input('id');

        return !is_null(Feature::find($featureId));
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function update(Request $request): bool
    {
        $featureId = $request->input('id');
        $featureName = $request->input('name');

        return
            Feature::find($featureId)->update(['name' => $featureName]);

    }

    /**
     * @param string $id
     * @return array|false
     */
    public function getFeatureById(string $id): array | false
    {
        $feature = Feature::find($id);

        return !is_null($feature) ? $feature->toArray() : false;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        return Feature::find($id)->delete();
    }

    /**
     * @param string $id
     * @return bool
     */
    public function canThisFeatureBeDeleted(string $id): bool
    {
        return Feature::find($id)->properties()->count() === 0;
    }
}
