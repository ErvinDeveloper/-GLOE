<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Dictionary\StoreRequest;
use App\Http\Requests\Admin\Dictionary\UpdateRequest;
use App\Models\Dictionary;
use App\Services\DictionaryService;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DictionaryController extends Controller
{
    use Response;

    public function __construct(private readonly DictionaryService $dictionaryService)
    {
    }

    public function index(): View
    {
        $dictionary = Dictionary::all();
        return view('admin.dictionary.index', compact('dictionary'));
    }

    public function create(): View
    {
        return view('admin.dictionary.create');
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $dictionary = $this->dictionaryService->store($request);
        return $this->responseSuccess([
            'redirect' => route('admin.dictionary.edit', $dictionary)
        ]);
    }

    public function edit(Dictionary $dictionary): View
    {
        return view('admin.dictionary.edit', compact('dictionary'));
    }

    public function update(
        Dictionary        $dictionary,
        UpdateRequest     $request
    ): JsonResponse
    {
        $this->dictionaryService->update($dictionary, $request);
        return $this->responseSuccess([
            'message' => 'Dictionary updated'
        ]);
    }

    public function delete(Dictionary $dictionary): RedirectResponse
    {
        $this->dictionaryService->delete($dictionary);
        return redirect()->route('admin.dictionary.index');
    }
}
