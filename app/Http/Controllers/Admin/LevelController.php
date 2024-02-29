<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Level\StoreRequest;
use App\Http\Requests\Admin\Level\UpdateRequest;
use App\Models\Level;
use App\Repository\LevelRepository;
use App\Services\LevelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LevelController extends Controller
{

    public function __construct(
        private readonly LevelRepository $levelRepository,
        private readonly LevelService    $levelService
    )
    {
    }

    public function index(): View
    {
        $levels = $this->levelRepository->getWithPagination();
        return view('admin.level.index', compact('levels'));
    }

    public function create(): View
    {
        return view('admin.level.create');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $this->levelService->store($request);
        return redirect()
            ->route('admin.level.index')
            ->withSuccess('Level added');
    }

    public function edit(Level $level): View
    {
        return view('admin.level.edit', compact('level'));
    }

    public function update(
        Level         $level,
        UpdateRequest $request
    ): RedirectResponse
    {
        $this->levelService->update($request, $level);
        return redirect()
            ->back()
            ->withSuccess('Level updated');
    }

    public function delete(Level $level): RedirectResponse
    {
        $level->delete();
        return redirect()
            ->back()
            ->withWarning('Level deleted');
    }
}
