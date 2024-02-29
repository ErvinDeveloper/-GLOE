<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreRequest;
use App\Http\Requests\Admin\Category\UpdateRequest;
use App\Models\Category;
use App\Repository\CategoryRepository;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly CategoryService $categoryService
    )
    {
    }

    public function index(): View
    {
        $categories = $this->categoryRepository->getWithPagination();
        return view('admin.category.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.category.create');
    }

    public function store(
        StoreRequest $request
    ): RedirectResponse
    {
        $this->categoryService->store($request);
        return redirect()->route('admin.category.index');
    }

    public function edit(Category $category): View
    {
        return view('admin.category.edit', compact('category'));
    }

    public function update(
        Category      $category,
        UpdateRequest $request
    ): RedirectResponse
    {
        $this->categoryService->update($category, $request);
        return redirect()->back()->withSuccess('Category updated');
    }

    public function delete(Category $category): RedirectResponse
    {
        $this->categoryService->delete($category);
        return redirect()->route('admin.category.index')->withWarning('Category deleted');
    }
}
