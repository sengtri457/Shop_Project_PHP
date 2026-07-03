<?php

namespace App\Controllers;

use App\Models\Category;
use App\Core\Response;

class CategoryController
{
    public function index(): void
    {
        Response::json(Category::all());
    }

    public function show(int $id): void
    {
        $category = Category::find($id);

        if (!$category) {
            Response::error('Category not found', 404);
            return;
        }

        $category['children'] = Category::children($id);
        $category['products'] = Category::products($id);

        Response::json($category);
    }
}
