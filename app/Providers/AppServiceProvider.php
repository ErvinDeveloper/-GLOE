<?php

namespace App\Providers;

use App\Actions\AudioPlayer;
use App\Actions\EditorJS;
use App\Actions\VideoPlayer;
use App\Actions\YoutubeIframe;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DictionaryController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TestingController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Repository\AuthorRepository;
use App\Repository\CategoryRepository;
use App\Repository\LevelRepository;
use App\Repository\NewsRepository;
use App\Repository\PostRepository;
use App\Repository\QuestionRepository;
use App\Repository\SettingRepository;
use App\Repository\TestingRepository;
use App\Repository\ThemeRepository;
use App\Repository\UserRepository;
use App\Services\AuthorService;
use App\Services\CategoryService;
use App\Services\DictionaryService;
use App\Services\EditorJSService;
use App\Services\FavoriteService;
use App\Services\FileService;
use App\Services\LevelService;
use App\Services\NewService;
use App\Services\PostService;
use App\Services\QuestionService;
use App\Services\ResizeImageService;
use App\Services\SettingService;
use App\Services\TestingService;
use App\Services\ThemeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(PostService::class, function () {
            return new PostService(new ResizeImageService(), new FileService(), new EditorJSService());
        });

        $this->app->bind(QuestionController::class, function () {
            return new QuestionController(new QuestionService(), new QuestionRepository(new YoutubeIframe(), new AudioPlayer(), new VideoPlayer(), new EditorJS()));
        });

        $this->app->bind(AuthorController::class, function () {
            return new AuthorController(new AuthorRepository(), new AuthorService());
        });

        $this->app->bind(CategoryController::class, function () {
            return new CategoryController(new CategoryRepository(), new CategoryService());
        });

        $this->app->bind(DictionaryController::class, function () {
            return new DictionaryController(new DictionaryService());
        });

        $this->app->bind(LevelController::class, function () {
            return new LevelController(new LevelRepository(), new LevelService());
        });

        $this->app->bind(NewsController::class, function () {
            return new NewsController(new NewsRepository(), new NewService());
        });

        $this->app->bind(PostController::class, function () {
            return new PostController(new PostRepository(), new PostService(new ResizeImageService(), new FileService(), new EditorJSService()));
        });

        $this->app->bind(SettingController::class, function () {
            return new SettingController(new SettingRepository(), new SettingService(new SettingRepository()));
        });

        $this->app->bind(SettingService::class, function () {
            return new SettingService(new SettingRepository());
        });

        $this->app->bind(TestingController::class, function () {
            return new TestingController(new TestingRepository(), new TestingService());
        });

        $this->app->bind(ThemeController::class, function () {
            return new ThemeController(new ThemeRepository(), new ThemeService());
        });

        $this->app->bind(FavoriteController::class, function () {
            return new FavoriteController(new FavoriteService());
        });

        $this->app->bind(QuestionRepository::class, function () {
            return new QuestionRepository(new YoutubeIframe(), new AudioPlayer(), new VideoPlayer(), new EditorJS());
        });

        $this->app->bind(\App\Http\Controllers\TestingController::class, function () {
            return new \App\Http\Controllers\TestingController(
                new TestingRepository(),
                new QuestionRepository(new YoutubeIframe(), new AudioPlayer(), new VideoPlayer(), new EditorJS()),
                new UserRepository()
            );
        });

        $this->app->bind(ProfileController::class, function () {
            return new ProfileController(new UserRepository());
        });

        $this->app->bind(SearchController::class, function () {
            return new SearchController(new PostRepository());
        });

    }
}
