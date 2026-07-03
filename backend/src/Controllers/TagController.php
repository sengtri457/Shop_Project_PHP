<?php

namespace App\Controllers;

use App\Models\Tag;
use App\Core\Response;
use App\Core\Auth;

class TagController
{
    public function index(): void
    {
        Response::json(Tag::all());
    }

    public function show(int $id): void
    {
        $tag = Tag::find($id);

        if (!$tag) {
            Response::error('Tag not found', 404);
            return;
        }

        Response::json($tag);
    }

    public function store(): void
    {
        Auth::requireAdmin();

        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['name'])) {
            Response::error('name is required');
            return;
        }

        $existing = Tag::findByNames([$body['name']]);

        if (!empty($existing)) {
            Response::json($existing[0]);
            return;
        }

        $id = Tag::create($body['name']);

        Response::json(Tag::find($id), 201);
    }
}
