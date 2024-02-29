<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Theme;
use App\Repository\CategoryRepository;
use App\Traits\CurrentCategory;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController
{

    use Response, CurrentCategory;

    public function __invoke(
        string             $slug,
        Request            $request,
        CategoryRepository $categoryRepository
    ): View|JsonResponse
    {
        $category = $categoryRepository->getBySlug($slug);
        if (empty($category)) {
            abort(404);
        }

        $this->setCurrentCategory($category);

        if ($request->has('page')) {
            return $this->getOnlyPosts($request, $category);
        }

        return $this->getFullPage($request, $category);
    }

    private function getFullPage(Request $request, object $category): View
    {
        $params = $category->getParamsForFiltering($request);

        $levels = Level::all();
        $themes = Theme::all();

        $postsQuery = $category->posts()->isPublished();
        $posts = $postsQuery->filterByThemeAndLevel($params)->paginate(config('app.per_page'));
        $posts->appends(request()->input());

        return view('category', compact('category', 'posts', 'levels', 'themes', 'params'));
    }

    private function getOnlyPosts(Request $request, object $category): JsonResponse
    {
        $postsQuery = $category->posts();

        $params = $category->getParamsForFiltering($request);
        $posts = $postsQuery->filterByThemeAndLevel($params)->paginate(config('app.per_page'));
        $posts->appends(request()->input());

        return $this->responseSuccess([
            'html' => ($posts->count() ? view('post.list', compact('posts'))->render() : null),
            'next_page_url' => $posts->nextPageUrl()
        ]);
    }

}
