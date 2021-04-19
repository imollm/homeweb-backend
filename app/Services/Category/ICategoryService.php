<?php


namespace App\Services\Category;


use App\Models\Category;
use App\Models\Property;
use Illuminate\Http\Request;

interface ICategoryService
{
    public function validatePostCategoryData(Request $request);
    public function validatePutCategoryData(Request $request);
    public function categoryExistsById(string $id): bool;
    public function categoryExistsByName(string $name): bool;
    public function hasThisCategoryProperties(string $id): bool;
    public function delete(string $id): bool;
    public function getAllCategories(): array;
    public function create(Request $request): bool;
    public function update(Request $request): bool;
    public function getCategoryById(string $id): array;
    public function getPropertiesByCategoryName(string $name): array;
    public function getPropertiesByCategoryId(string $id): array;
}
