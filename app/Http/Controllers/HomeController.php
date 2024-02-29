<?php

namespace App\Http\Controllers;

use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;
use App\Repository\PostRepository;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(
        CategoryRepository $categoryRepository,
        PostRepository     $postRepository,
        NewsRepository     $newsRepository
    ): View
    {
        $categories = $categoryRepository->getParentCategories();

        $lastPosts = $postRepository->last();
        $lastPost = $lastPosts->first();

        $viewedPosts = $postRepository->viewed();

        $news = $newsRepository->getAllNews();
        $videos = $postRepository->videos();

        return view('home',
            compact('categories', 'lastPosts', 'viewedPosts', 'lastPost', 'news', 'videos')
        );
    }
}
