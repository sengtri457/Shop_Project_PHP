<?php

namespace App\Controllers;

use App\Models\ProductReview;
use App\Models\Product;
use App\Core\Response;
use App\Core\Auth;

class ProductReviewController
{
    public function index(int $productId): void
    {
        $product = Product::find($productId);
        if (!$product) {
            Response::error('Product not found', 404);
            return;
        }

        $reviews = ProductReview::findByProductId($productId);
        $summary = ProductReview::getAverageRating($productId);

        Response::json([
            'summary' => $summary,
            'reviews' => $reviews
        ]);
    }

    public function store(int $productId): void
    {
        $customerId = Auth::id();
        if (!$customerId) {
            Response::error('Unauthorized. Please login to submit a review.', 401);
            return;
        }

        $product = Product::find($productId);
        if (!$product) {
            Response::error('Product not found', 404);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $rating = isset($body['rating']) ? (int)$body['rating'] : 0;
        $comment = trim($body['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            Response::error('Rating must be an integer between 1 and 5');
            return;
        }

        try {
            $id = ProductReview::create([
                'product_id' => $productId,
                'customer_id' => $customerId,
                'rating' => $rating,
                'comment' => $comment ?: null
            ]);

            Response::json([
                'message' => 'Review submitted successfully',
                'id' => $id
            ], 201);
        } catch (\Exception $e) {
            Response::error($e->getMessage());
        }
    }
}
