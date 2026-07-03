<?php

namespace App\Controllers;

use App\Models\Category;
use App\Core\Response;
use App\Core\Auth;

class CategoryController
{
    public function index(): void
    {
        Response::json(Category::all());
    }

    public function tree(): void
    {
        Response::json(Category::tree());
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

    public function store(): void
    {
        Auth::requireAdmin();

        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['name'])) {
            Response::error('name is required');
            return;
        }

        $id = Category::create($body);

        Response::json(Category::find($id), 201);
    }

    public function update(int $id): void
    {
        Auth::requireAdmin();

        $category = Category::find($id);

        if (!$category) {
            Response::error('Category not found', 404);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true);

        Category::update($id, $body);

        Response::json(Category::find($id));
    }

    public function destroy(int $id): void
    {
        Auth::requireAdmin();

        $category = Category::find($id);

        if (!$category) {
            Response::error('Category not found', 404);
            return;
        }

        Category::delete($id);

        Response::json(['message' => 'Category deleted']);
    }
}
