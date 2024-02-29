<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Post;
use App\Repository\PostRepository;
use Illuminate\View\View;

class SearchController extends Controller
{

    public function __construct(private readonly PostRepository $postRepository)
    {
    }

    public function __invoke(SearchRequest $request): View
    {
        $query = $request->input('query');
        $posts = $this->postRepository->search($request);
        return view('search', compact('posts', 'query'));
    }
}
