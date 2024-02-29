<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Datatables\GetRequest;
use App\Http\Requests\Admin\Post\StoreRequest;
use App\Http\Requests\Admin\Post\UpdateRequest;
use App\Models\Category;
use App\Models\Level;
use App\Models\Post;
use App\Models\Theme;
use App\Repository\PostRepository;
use App\Services\EditorJSService;
use App\Services\PostService;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    use Response;

    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly PostService    $postService
    )
    {
    }

    public function index(?Category $category): View
    {
        $columns = $this->postRepository->datatableColumns();
        return view('admin.parts.datatables', compact('columns', 'category'));
    }

    public function datatables(GetRequest $request): JsonResponse
    {
        if (!$request->ajax()) {
            return $this->responseError([
                'message' => 'The request is not ajax'
            ]);
        }

        $response = $this->postService->getDataForDatatables($request);

        return response()->json($response);
    }

    public function create(): View
    {
        $categories = Category::all();
        $levels = Level::all();
        $themes = Theme::all();

        return view('admin.post.create', compact('categories', 'levels', 'themes'));
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $post = $this->postService->store($request);
        return $this->responseSuccess([
            'redirect' => route('admin.post.edit', $post)
        ]);
    }

    public function edit(Post $post): View
    {
        $categories = Category::all();
        $levels = Level::all();
        $themes = Theme::all();
        return view('admin.post.edit', compact('post', 'categories', 'levels', 'themes'));
    }

    public function update(
        Post          $post,
        UpdateRequest $request
    ): JsonResponse
    {
        $this->postService->update($request, $post);
        return $this->responseSuccess([
            'message' => 'Post updated'
        ]);
    }

    public function delete(Post $post): RedirectResponse
    {
        $this->postService->delete($post);
        return redirect()->back();
    }

    public function deleteStorage(
        Post $post,
        int  $storageId
    ): JsonResponse
    {
        $storage = $post->storage()->find($storageId);
        if (empty($storage)) {
            abort(404);
        }

        $isDeleted = $this->postService->deleteFileFromStorage($storage);
        if ($isDeleted) {
            return $this->responseSuccess(['message' => 'Additional material has been deleted']);
        }

        return $this->responseError(['message' => 'error.']);
    }

    public function editorJSUpload(Request $request): JsonResponse
    {
        if ($result = $this->postService->editorJSUpload($request)) {
            return $this->responseSuccess($result);
        }

        return $this->responseError(['message' => 'The file was not uploaded']);
    }

    public function deleteImage(Post $post, PostService $postService): JsonResponse
    {
        $isDeleted = $postService->deleteMainImage($post);
        if ($isDeleted) {
            return $this->responseSuccess(['message' => 'Image deleted']);
        }

        return $this->responseError(['message' => 'Image empty']);
    }
}
