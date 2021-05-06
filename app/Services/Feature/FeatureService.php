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
     * @var Feature
     */
    private Feature $feature;

    /**
     * FeatureService constructor.
     * @param Feature $feature
     */
    public function __construct(Feature $feature)
    {
        $this->feature = $feature;
    }

    /**
     * @return array
     */
    public function getAllFeatures(): array
    {
        return $this->feature
                    ->with('properties')
                    ->with('propertiesCount')
                    ->get()->toArray();
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
        $feature = $this->feature->create([
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

        return !is_null($this->feature->find($featureId));
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
            $this->feature->find($featureId)->update(['name' => $featureName]);

    }

    /**
     * @param string $id
     * @return array|false
     */
    public function getFeatureById(string $id): array | false
    {
        $feature = $this->feature->find($id);

        return !is_null($feature) ? $feature->toArray() : false;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        return $this->feature->find($id)->delete();
    }

    /**
     * @param string $id
     * @return bool
     */
    public function canThisFeatureBeDeleted(string $id): bool
    {
        return $this->feature->find($id)->properties()->count() === 0;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function setFeaturesToBeSaved(Request $request): array
    {
        $featuresToBeSaved = [];
        $features = $request->input('features');

        foreach ($features as $index =>  $feature) {
            if ($feature) {
                array_push($featuresToBeSaved, $index + 1);
            } else if (is_numeric($feature)) {
                array_push($featuresToBeSaved, $feature);
            }
        }
        return $featuresToBeSaved;
    }
}
