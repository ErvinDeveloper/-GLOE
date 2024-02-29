<?php

namespace App\Telegram;

use App\Models\Category;
use App\Models\Post;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class Handler extends WebhookHandler
{

    public function categories(): void
    {
        $categories = Category::all();
        $buttons = $categories->map(function ($category) {
            return Button::make($category->title)->action('category')->param('id', $category->id);
        });

        Telegraph::message('Кликните на нужную категорию')
            ->keyboard(Keyboard::make()->buttons($buttons))->send();

    }

    public function category(): void
    {
        $categoryId = $this->data->get('id');
        $category = Category::query()->find($categoryId);

        $buttons = $category->posts->map(function ($post) use ($category) {
            return Button::make($post->title)->url(route('post.show', ['categorySlug' => $category->slug, 'postSlug' => $post->slug]));
        });

        Telegraph::message('*' . $category->title . '*')
            ->keyboard(Keyboard::make()->buttons($buttons))->send();

    }

    public function last_posts()
    {
        $buttons = Post::query()->with('category')->orderBy('id', 'desc')->limit(10)->get()->map(function ($post) {
            return Button::make($post->title)->url(route('post.show', ['categorySlug' => $post->category->slug, 'postSlug' => $post->slug]));
        });

        Telegraph::message('*Последние материалы*')
            ->keyboard(Keyboard::make()->buttons($buttons))->send();
    }

    protected function handleUnknownCommand($text): void
    {
        $this->reply('Неверная команда!');
    }

}
