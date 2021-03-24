<?php


namespace App\Services\Category;


use App\Models\Category;
use Illuminate\Http\Request;

interface ICategoryService
{
    public function validatePostCategoryData(Request $request);
    public function categoryExistsById(string $id): bool;
    public function categoryExistsByName(string $name): bool;
    public function hasThisCategoryProperties(string $id): bool;
    public function delete(string $id): bool;
    public function getAllCategories(): array;
    public function create(string $name): bool;
    public function getCategoryById(string $id): array;
}
