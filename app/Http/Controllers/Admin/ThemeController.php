<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Theme\StoreRequest;
use App\Http\Requests\Admin\Theme\UpdateRequest;
use App\Models\Theme;
use App\Repository\ThemeRepository;
use App\Services\ThemeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ThemeController extends Controller
{

    public function __construct(
        private readonly ThemeRepository $themeRepository,
        private readonly ThemeService    $themeService
    )
    {
    }

    public function index(): View
    {
        $themes = $this->themeRepository->getWithPagination();
        return view('admin.theme.index', compact('themes'));
    }

    public function create(): View
    {
        return view('admin.theme.create');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $this->themeService->store($request);
        return redirect()->route('admin.theme.index');
    }

    public function edit(Theme $theme): View
    {
        return view('admin.theme.edit', compact('theme'));
    }

    public function update(UpdateRequest $request, Theme $theme): RedirectResponse
    {
        $this->themeService->update($request, $theme);
        return redirect()->back()->withSuccess('Тема обновлена');
    }

    public function delete(Theme $theme)
    {
        $this->themeService->delete($theme);
        return redirect()->route('admin.theme.index')->withWarning('Тема удалена');
    }
}
