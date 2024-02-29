<?php

namespace App\Http\Controllers;

use App\Actions\PostViewed;
use App\Repository\PostRepository;
use App\Traits\CurrentCategory;
use Illuminate\View\View;

class PostController extends Controller
{

    use CurrentCategory;

    public function __invoke(
        string         $categorySlug,
        string         $postSlug,
        PostRepository $postRepository,
        PostViewed     $postViewed
    ): View
    {
        $post = $postRepository->findBySlug($postSlug);
        if (empty($post)) {
            abort(404);
        }

        $this->setCurrentCategory($post->category);

        $postViewed->handler($post);

        return view('post.show', compact('post'));
    }
}
