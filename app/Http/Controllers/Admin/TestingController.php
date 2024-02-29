<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Test\StoreRequest;
use App\Http\Requests\Admin\Test\UpdateRequest;
use App\Models\Test;
use App\Repository\TestingRepository;
use App\Services\TestingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class TestingController extends Controller
{
    public function __construct(
        private readonly TestingRepository $testingRepository,
        private readonly TestingService    $testingService
    )
    {
    }

    public function index(): View
    {
        $tests = $this->testingRepository->all();
        return view('admin.testing.index', compact('tests'));
    }

    public function create(): View
    {
        return view('admin.testing.create');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $test = $this->testingService->store($request);
        return redirect()
            ->route('admin.question.create', $test)
            ->withSuccess('Тест создан');
    }

    public function delete(Test $test): RedirectResponse
    {
        $this->testingService->delete($test);
        return redirect()
            ->route('admin.testing.index')
            ->withWarning('Тест удален');
    }

    public function edit(Test $test): View
    {
        return view('admin.testing.edit', compact('test'));
    }

    public function update(Test $test, UpdateRequest $request): RedirectResponse
    {
        $this->testingService->update($test, $request);
        return redirect()
            ->back()
            ->withSuccess('Тест обновлен');
    }
}
