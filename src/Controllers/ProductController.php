<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Variant;
use App\Core\Response;

class ProductController
{
    public function index(): void
    {
        $categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;
        $search = $_GET['search'] ?? null;
        $brand = $_GET['brand'] ?? null;

        $products = Product::all($categoryId, $search, $brand);

        $result = array_map(function ($product) {
            $product['variants'] = Product::variants($product['id']);
            return $product;
        }, $products);

        Response::json($result);
    }

    public function show(int $id): void
    {
        $product = Product::find($id);

        if (!$product) {
            Response::error('Product not found', 404);
            return;
        }

        $product['variants']   = Product::variants($id);
        $product['categories'] = Product::categories($id);
        $product['tags']       = Product::tags($id);

        Response::json($product);
    }

    public function store(): void
    {
        $body = json_decode(file_get_contents('php://input'), true);

        $id = Product::create($body);

        if (!empty($body['variants'])) {
            foreach ($body['variants'] as $variant) {
                $variant['product_id'] = $id;
                Variant::create($variant);
            }
        }

        $product = Product::find($id);
        $product['variants'] = Product::variants($id);

        Response::json($product, 201);
    }
}
