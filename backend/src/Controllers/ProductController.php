<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Variant;
use App\Core\Response;

class ProductController
{
    public function index(): void
    {
        $result = Product::all(
            categoryId: isset($_GET['category_id']) ? (int) $_GET['category_id'] : null,
            search: $_GET['search'] ?? null,
            brand: $_GET['brand'] ?? null,
            minPrice: isset($_GET['min_price']) ? (float) $_GET['min_price'] : null,
            maxPrice: isset($_GET['max_price']) ? (float) $_GET['max_price'] : null,
            inStock: isset($_GET['in_stock']) ? filter_var($_GET['in_stock'], FILTER_VALIDATE_BOOLEAN) : null,
            tagIds: !empty($_GET['tag_ids']) ? array_map('intval', explode(',', $_GET['tag_ids'])) : null,
            sortBy: $_GET['sort_by'] ?? 'created_at',
            sortOrder: $_GET['sort_order'] ?? 'desc',
            page: max(1, (int) ($_GET['page'] ?? 1)),
            limit: min(100, max(1, (int) ($_GET['limit'] ?? 20))),
            gender: $_GET['gender'] ?? null
        );

        $result['data'] = array_map(function ($product) {
            $product['variants'] = Product::variants($product['id']);
            return $product;
        }, $result['data']);

        Response::json($result);
    }

    public function bestSellers(): void
    {
        $limit = min(50, max(1, (int) ($_GET['limit'] ?? 10)));
        $products = Product::bestSellers($limit);
        Response::json($products);
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

        $error = $this->validateVariantsSkus($body['variants'] ?? []);
        if ($error) {
            Response::error($error);
            return;
        }

        $id = Product::create($body);

        if (!empty($body['variants'])) {
            foreach ($body['variants'] as $variant) {
                $variant['product_id'] = $id;
                Variant::create($variant);
            }
        }

        Product::syncCategories($id, $body['category_ids'] ?? []);
        Product::syncTags($id, $body['tag_ids'] ?? []);

        $product = Product::find($id);
        $product['variants']   = Product::variants($id);
        $product['categories'] = Product::categories($id);
        $product['tags']       = Product::tags($id);

        Response::json($product, 201);
    }

    public function update(int $id): void
    {
        $product = Product::find($id);

        if (!$product) {
            Response::error('Product not found', 404);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true);

        Product::update($id, $body);

        if (isset($body['variants'])) {
            $existing = Product::variants($id);
            $existingIds = array_column($existing, 'id');

            $newVariants = [];

            foreach ($body['variants'] as $variant) {
                if (!empty($variant['id']) && in_array($variant['id'], $existingIds)) {
                    Variant::update($variant['id'], $variant);
                } else {
                    $newVariants[] = $variant;
                }
            }

            if (!empty($newVariants)) {
                $error = $this->validateVariantsSkus($newVariants);
                if ($error) {
                    Response::error($error);
                    return;
                }

                foreach ($newVariants as $variant) {
                    $variant['product_id'] = $id;
                    Variant::create($variant);
                }
            }
        }

        if (isset($body['category_ids'])) {
            Product::syncCategories($id, $body['category_ids']);
        }

        if (isset($body['tag_ids'])) {
            Product::syncTags($id, $body['tag_ids']);
        }

        $product = Product::find($id);
        $product['variants']   = Product::variants($id);
        $product['categories'] = Product::categories($id);
        $product['tags']       = Product::tags($id);

        Response::json($product);
    }

    public function destroy(int $id): void
    {
        $product = Product::find($id);

        if (!$product) {
            Response::error('Product not found', 404);
            return;
        }

        Product::delete($id);

        Response::json(['message' => 'Product deleted']);
    }

    private function validateVariantsSkus(array $variants): ?string
    {
        $skus = array_filter(array_column($variants, 'sku'));

        if (empty($skus)) {
            return null;
        }

        $existing = Variant::findBySkus($skus);

        if (!empty($existing)) {
            $dupes = array_column($existing, 'sku');
            return 'Duplicate SKU(s): ' . implode(', ', $dupes);
        }

        return null;
    }
}
