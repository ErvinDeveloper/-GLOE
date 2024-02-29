<?php

namespace App\Providers;

use App\Actions\EditorJS;
use App\Models\Category;
use App\Models\Setting;
use App\Repository\PostRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    public function boot(PostRepository $postRepository): void
    {
        View::composer('parts.word_of_week', function ($view) {
            $settings = Setting::query()->first();
            if (isset($settings->word_of_week)) {
                $settings->word_of_week = (new EditorJS())->set($settings->word_of_week)->render();
                $settings->word_of_week_lat = (new EditorJS())->set($settings->word_of_week_lat)->render();
                $settings->word_of_week = str_replace('&lt;br&gt;', '', $settings->word_of_week);
                $settings->word_of_week_lat = str_replace('&lt;br&gt;', '', $settings->word_of_week_lat);
            }
            $view->with([
                'settings' => $settings
            ]);
        });

        View::composer(['parts.menu', 'parts.footer'], function ($view) {
            $categories = Category::all();

            $view->with([
                'categories' => $categories
            ]);
        });

        View::composer(['parts.home_sections.one_post_from_each_level'], function ($view) use ($postRepository) {
            $view->with([
                'posts' => $postRepository->getPostsFromEachLevel()
            ]);
        });

        View::composer(['parts.home_sections.recommend_posts'], function ($view) use ($postRepository) {
            $view->with([
                'posts' => $postRepository->recommend()
            ]);
        });

        View::composer(['parts.home_sections.posts_on_themes_you_are_viewing'], function ($view) use ($postRepository) {
            $view->with([
                'posts' => $postRepository->getPostsOnThemesYouAreViewing()
            ]);
        });

        View::composer(['parts.home_sections.one_post_from_each_theme'], function ($view) use ($postRepository) {
            $view->with([
                'posts' => $postRepository->getPostsFromEachTheme()
            ]);
        });
    }
}
