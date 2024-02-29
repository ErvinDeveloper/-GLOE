<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\News\StoreRequest;
use App\Http\Requests\Admin\News\UpdateRequest;
use App\Models\News;
use App\Repository\NewsRepository;
use App\Services\NewService;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NewsController extends Controller
{

    public function __construct(
        private readonly NewsRepository $newsRepository,
        private readonly NewService     $newService
    )
    {
    }

    use Response;

    public function index(): View
    {
        $news = $this->newsRepository->getWithPagination();
        return view('admin.news.index', compact('news'));
    }

    public function create(): View
    {
        return view('admin.news.create');
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $new = $this->newService->store($request);
        return $this->responseSuccess([
            'redirect' => route('admin.news.edit', $new)
        ]);
    }

    public function edit(News $new): View
    {
        return view('admin.news.edit', compact('new'));
    }

    public function update(
        News          $new,
        UpdateRequest $request
    ): JsonResponse
    {
        $this->newService->update($new, $request);
        return $this->responseSuccess([
            'message' => 'New updated'
        ]);
    }

    public function delete(News $new): RedirectResponse
    {
        $this->newService->delete($new);
        return redirect()->route('admin.news.index');
    }
}
