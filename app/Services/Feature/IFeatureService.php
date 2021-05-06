<?php


namespace App\Services\Feature;


use Illuminate\Http\Request;

interface IFeatureService
{
    public function getAllFeatures(): array;
    public function validatePostData(Request $request);
    public function create(Request $request): bool;
    public function existsThisFeature(Request $request): bool;
    public function update(Request $request): bool;
    public function getFeatureById(string $id): array | false;
    public function delete(string $id): bool;
    public function canThisFeatureBeDeleted(string $id): bool;
    public function setFeaturesToBeSaved(Request $request): array;
}
