<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Author\GetRequest;
use App\Http\Requests\Admin\Author\StoreRequest;
use App\Http\Requests\Admin\Author\UpdateRequest;
use App\Models\Author;
use App\Repository\AuthorRepository;
use App\Services\AuthorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthorController extends Controller
{

    public function __construct(
        private readonly AuthorRepository $authorRepository,
        private readonly AuthorService $authorService
    )
    {
    }

    public function index(): View
    {
        $authors = $this->authorRepository->getWithPagination();
        return view('admin.author.index', compact('authors'));
    }

    public function create(): View
    {
        return view('admin.author.create');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $this->authorService->store($request);
        return redirect()->route('admin.author.index')->withSuccess('Author added');
    }

    public function edit(Author $author): View
    {
        return view('admin.author.edit', compact('author'));
    }

    public function update(
        Author        $author,
        UpdateRequest $request
    ): RedirectResponse
    {
        $this->authorService->update($request, $author);
        return redirect()->back()->withSuccess('Author updated');
    }

    public function delete(Author $author): RedirectResponse
    {
        $this->authorService->delete($author);
        return redirect()->route('admin.author.index')->withWarning('Author deleted');
    }

    public function getAuthorsInJson(GetRequest $request): JsonResponse
    {
        $authors = $this->authorRepository->findByName($request->input('query'));
        return response()->json($authors);
    }
}
