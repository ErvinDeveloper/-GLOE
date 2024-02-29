<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\FavoriteService;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;

class FavoriteController extends Controller
{
    use Response;

    public function __construct(private readonly FavoriteService $favoriteService)
    {
    }

    public function create(Post $post): JsonResponse
    {
        if ($this->favoriteService->create($post)) {
            return $this->responseSuccess([
                'message' => __('favorite.post.added_to_favorites')
            ]);
        }

        return $this->responseError([
            'message' => __('favorite.post.already_in_favorites')
        ]);
    }

    public function delete(Post $post): JsonResponse
    {
        if ($this->favoriteService->delete($post)) {
            return $this->responseSuccess([
                'message' => __('favorite.post.deleted_from_favorites')
            ]);
        }

        return $this->responseError([
            'message' => __('favorite.not_added_to_favorites')
        ]);
    }

    public function checkPostsForFavorites(string $ids): JsonResponse
    {
        return response()->json($this->favoriteService->checkPostsForFavorites($ids));
    }

}
