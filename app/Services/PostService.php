<?php

namespace App\Services;

use App\Http\Requests\Admin\Datatables\GetRequest;
use App\Http\Requests\Admin\Post\StoreRequest;
use App\Http\Requests\Admin\Post\UpdateRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

readonly class PostService
{

    public function __construct(
        private ResizeImageService $resizeImageService,
        private FileService        $fileService,
        private EditorJSService    $editorJSService
    )
    {
    }

    public function getDataForDatatables(GetRequest $request): array
    {
        $data = $request->validated();

        $length = $data['length'];
        $start = $data['start'];
        $columnIndex = $data['order'][0]['column'] ?? 'id';
        $columnName = $data['columns'][$columnIndex]['data'];
        $columnSortOrder = $data['order'][0]['dir'];
        $searchValue = $data['search']['value'];

        $callback = function ($row) {
            return [
                'id' => $row->id,
                'title' => $row->title,
                'user_id' => $row->user->name,
                'category_id' => (!empty($row->category_id) ? '<a href="' . route('admin.post.index', ['category' => $row->category]) . '">' . $row->category->title . '</a>' : '[Не выбрана]'),
                'level_id' => (!empty($row->level_id) && !empty($row->level->title) ?
                    '<div style="background: ' . $row->level->color . '; color: #fff; width: 100px; padding: 5px; border-radius: 3px; text-align: center">' . $row->level->title . '</div>' : '[Не выбран]'),
                'actions' => view('admin.parts.actions', [
                    'edit' => route('admin.post.edit', $row),
                    'delete' => route('admin.post.delete', $row)
                ])->render()
            ];
        };

        if (isset($data['categoryId'])) {
            $query = Category::query()->find($data['categoryId'])->posts();
        } else {
            $query = Post::query();
        }


        $query->with(['user', 'category']);

        $query = $query->skip($start)->take($length);

        $countAll = $query->count();

        if (!empty($searchValue)) {
            $query->searchFilter($searchValue);
        }

        $countFiltered = $query->count();

        $rows = $query->orderBy($columnName, $columnSortOrder)->get()->map($callback);

        return [
            'draw' => $data['draw'],
            'iTotalRecords' => $countFiltered,
            'iTotalDisplayRecords' => $countAll,
            'aaData' => $rows
        ];
    }

    public function store(StoreRequest $request): Model
    {
        $data = $request->validated();
        $data['image'] = null;
        $data['slug'] = $this->getSlug($data);

        if ($request->file('image')) {
            $data['image'] = $this->uploadImage($request);
            $this->resizeImageService->resize($data['image']);
        }

        $post = auth()->user()->posts()->create($data);

        $this->addingFilesToStorage($request, $post);

        return $post;
    }

    public function update(UpdateRequest $request, Post $post): void
    {
        $data = $request->validated();

        $data['image'] = $post->image;
        $data['slug'] = $this->getSlug($data);

        if ($request->file('image')) {
            if ($this->hasImage($post)) {
                $this->deleteImage($post);
            }

            $data['image'] = $this->uploadImage($request);

            $this->resizeImageService->resize($data['image']);
        }

        $post->update($data);

        $this->addingFilesToStorage($request, $post);
    }

    public function delete(Post $post): bool
    {
        if ($post->delete() && !empty($post->image)) {
            return Storage::delete($post->image);
        }

        return false;
    }

    public function deleteFileFromStorage(\App\Models\Storage $storage): bool
    {
        $isDeleted = $storage->delete();
        if ($isDeleted) {
            return File::delete('storage/' . $storage->path);
        }

        return false;
    }

    public function deleteMainImage(Post $post): bool
    {
        if (!empty($post->image) && !Storage::exists($post->image)) {
            $post->image = '';
            $post->save();
            return false;
        }

        if (empty($post->image)) {
            return false;
        }

        if (File::delete('storage/' . $post->image)) {
            $this->resizeImageService->deleteResizeImages($post->image);
            $post->image = '';
            $post->save();

            return true;
        }

        return false;
    }


    public function editorJSUpload(Request $request): array|false
    {
        $path = $this->editorJSService->upload($request);
        if (empty($path)) {
            return false;
        }

        if ($this->editorJSService->isImage($path) || $request->has('isAttach')) {
            return [
                'file' => [
                    'url' => asset('storage/' . $path),
                    'extension' => File::extension('storage/' . $path),
                    'size' => File::size('storage/' . $path),
                    'name' => File::name('storage/' . $path)
                ]
            ];
        }

        return ['url' => asset('storage/' . $path)];
    }

    private function setAuthor(UpdateRequest|StoreRequest $request, Post $post): void
    {
        $post->author_id = $request->input('author_id');
        $post->update();
    }

    private function addingFilesToStorage(UpdateRequest|StoreRequest $request, Post $post): void
    {
        $storageInsert = $this->fileService->upload($post, $request);

        if (!empty($storageInsert)) {
            $post->storage()->createMany($storageInsert);
        }
    }

    private function getSlug(array $data): string
    {
        if (empty($data['slug'])) {
            return Str::slug($data['title']);
        }

        return $data['slug'];
    }

    private function deleteImage(Post $post)
    {
        if (Storage::delete($post->image)) {
            return $this->resizeImageService->deleteResizeImages($post->image);
        }

        return false;
    }

    private function hasImage(Post $post): bool
    {
        return !empty($post->image) && Storage::has($post->image);
    }

    private function uploadImage(StoreRequest|UpdateRequest $request): false|string
    {
        $folder = date('Y/m');

        return $request->file('image')->storeAs(
            $folder,
            time() . '_' . Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension(),
            'public'
        );
    }

}
