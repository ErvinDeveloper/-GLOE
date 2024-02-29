<?php

namespace App\Services;

use App\Models\Post;
use Exception;
use Illuminate\Support\Collection;

class FavoriteService
{
    public function create(Post $post): ?bool
    {
        try {
            if ($this->isAdded($post)) {
                return false;
            }

            $favorite = $post->favorites()->create([
                'user_id' => auth()->user()->id
            ]);

            if ($favorite) {
                return true;
            }
        } catch (Exception $exception) {
            die($exception->getMessage());
        }

        return null;
    }

    public function delete(Post $post): ?bool
    {
        try {
            if (!$this->isAdded($post)) {
                return false;
            }

            $result = auth()
                ->user()
                ->favorites()
                ->where('favoriteable_type', Post::class)
                ->where('favoriteable_id', $post->id)
                ->delete();

            if ($result) {
                return true;
            }

        } catch (Exception $exception) {
            die($exception->getMessage());
        }

        return null;
    }

    public function checkPostsForFavorites(string $ids): Collection
    {
        $ids = explode(',', $ids);

        $favoriteIds = auth()
            ->user()
            ->favorites()
            ->where('favoriteable_type', Post::class)
            ->whereIn('favoriteable_id', $ids)
            ->select('favoriteable_id')
            ->get()
            ->pluck('favoriteable_id')
            ->toArray();

        return collect($ids)->map(function ($id) use ($favoriteIds) {
            return ['id' => $id, 'is_favorite' => in_array($id, $favoriteIds)];
        });
    }

    private function isAdded(Post $post): bool
    {
        if ($post->favorites()->where('user_id', auth()->user()->id)->count()) {
            return true;
        }

        return false;
    }

}
