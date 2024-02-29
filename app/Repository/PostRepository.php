<?php

namespace App\Repository;

use App\Actions\EditorJS;
use App\Actions\Tooltip;
use App\Http\Requests\SearchRequest;
use App\Models\Level;
use App\Models\Post;
use App\Models\Theme;
use App\Services\ResizeImageService;
use App\Traits\DateFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostRepository extends Repository
{
    use DateFormat;

    protected function getModelClass(): string
    {
        return Post::class;
    }

    public function datatableColumns(): array
    {
        return [
            'ID',
            'Title',
            'User',
            'Category',
            'Level',
            'Actions'
        ];
    }

    public function search(SearchRequest $request)
    {
        $data = $request->validated();
        $query = $data['query'];

        return $this->startConditions()->query()->searchFilter($query)->get();
    }

    public function findBySlug(string $slug): ?Model
    {
        $post = $this->startConditions()->query()->where('slug', $slug)->with(['category', 'theme', 'author'])->first();
        if (empty($post)) {
            return null;
        }

        $post->body = (new EditorJS())->set($post->body)->render();
        $post->body = (new Tooltip())->set($post->body)->replace();

        $post->body_lat = (new EditorJS())->set($post->body_lat)->render();
        $post->body_lat = (new Tooltip())->set($post->body_lat)->replace();

        $post->ppt = $post->storage()->onlyExtension(['ppt', 'pptx'])->get();
        $post->pdf = $post->storage()->onlyExtension('pdf')->get();

        return $post;
    }

    public function recommend(): Collection
    {
        $userLevel = auth()->user()->level();
        if (!$userLevel->count()) {
            return collect();
        }

        return $userLevel
            ->first()
            ->posts()
            ->isPublished()
            ->with('theme', 'level', 'author')
            ->orderBy('id', 'desc')
            ->limit(4)
            ->get();
    }

    public function last(): Collection
    {
        return $this->startConditions()->query()
            ->isPublished()
            ->with('theme', 'level', 'author')
            ->orderBy('id', 'desc')
            ->limit(4)
            ->get();
    }

    public function viewed(): Collection
    {
        if (!Auth::check()) {
            return collect();
        }

        return auth()->user()
            ->views()
            ->whereHas('viewable', function ($q) {
                $q->where('is_published', 1);
            })
            ->orderBy('updated_at', 'desc')
            ->limit(4)
            ->get();
    }

    public function getPostsOnThemesYouAreViewing(): Collection
    {
        $viewedThemes = DB::select("
                    SELECT theme_id
                    FROM views
                    RIGHT JOIN posts
                        ON posts.id = views.viewable_id
                    WHERE views.user_id = :user_id AND views.viewable_type = :viewable_type
                    GROUP BY theme_id
                    ORDER BY COUNT(theme_id) DESC", [
            'user_id' => auth()->user()->id,
            'viewable_type' => Post::class
        ]);

        $viewedThemeIds = collect($viewedThemes)->slice(0, 3)->pluck('theme_id')->toArray();
        return Post::query()->whereIn('theme_id', $viewedThemeIds)->isPublished()->orderBy('id', 'desc')->limit(3)->get();

    }

    public function videos(): Collection
    {
        return Post::query()->isPublished()->isVideo()->orderBy('id', 'desc')->limit(4)->get();
    }

    public function getPostsFromEachLevel(): Collection
    {

        $levels = [];

        $queryUnion = Level::query()->get()
            ->map(function ($level) use (&$levels) {
                $levels[$level->id] = $level;

                return "(SELECT
                id, title, title_lat, slug, image, excerpt, created_at, category_id, theme_id, author_id, level_id
                FROM posts WHERE level_id = " . $level->id . " ORDER BY id DESC LIMIT 1)";
            })->implode(" UNION ");

        $query = "SELECT
            p.id, p.title, p.title_lat, p.slug, p.image, p.level_id, p.excerpt, p.created_at,
            c.title AS category_title, c.slug AS category_slug,
            t.title AS theme_title,
            a.name AS author_name FROM ($queryUnion) AS p
            LEFT JOIN categories AS c
                ON
                    p.category_id = c.id
                LEFT JOIN themes AS t
                ON
                    p.theme_id = t.id
                LEFT JOIN authors AS a
                ON
                    p.author_id = a.id
        ";

        return collect(DB::select($query))->map(function ($post) use ($levels) {
            $level = $levels[$post->level_id];

            $post->date = $this->getDateAttribute($post->created_at);
            return (object)array_merge((array)$post, [
                'level_name' => $level->title,
                'level_color' => $level->color,
                'images' => (new ResizeImageService)->getResizeImages($post->image)
            ]);
        });

    }

    public function getPostsFromEachTheme(): Collection
    {

        $themes = [];

        $queryUnion = Theme::query()->get()
            ->map(function ($theme) use (&$themes) {
                $themes[$theme->id] = $theme;

                return "(SELECT
                id, title, title_lat, slug, image, excerpt, created_at, category_id, theme_id, author_id, level_id
                FROM posts WHERE theme_id = " . $theme->id . " ORDER BY id DESC LIMIT 1)";
            })->implode(" UNION ");

        $query = "SELECT
            p.id, p.title, p.title_lat, p.slug, p.image, p.level_id, p.excerpt, p.created_at, p.theme_id,
            c.title AS category_title, c.slug AS category_slug,
            l.title AS level_title, l.color AS level_color,
            a.name AS author_name FROM ($queryUnion) AS p
            LEFT JOIN categories AS c
                ON
                    p.category_id = c.id
                LEFT JOIN levels AS l
                ON
                    p.level_id = l.id
                LEFT JOIN authors AS a
                ON
                    p.author_id = a.id
        ";

        return collect(DB::select($query))->map(function ($post) use ($themes) {
            $theme = $themes[$post->theme_id];

            $post->date = $this->getDateAttribute($post->created_at);
            return (object)array_merge((array)$post, [
                'theme_title' => $theme->title,
                'images' => (new ResizeImageService)->getResizeImages($post->image)
            ]);
        });
    }

}
