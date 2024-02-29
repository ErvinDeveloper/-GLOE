<?php


use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

function activeClass(string|array $routes, string $class): ?string {

    $routeName = Route::currentRouteName();
    if (is_string($routes) && $routes === $routeName) {
        return $class;
    }

    if (is_array($routes) && in_array($routeName, $routes)) {
        return $class;
    }

    return null;
}

function humanFileSize(int $size): string
{
    if ($size >= 1073741824) {
        $fileSize = round($size / 1024 / 1024 / 1024,1) . 'GB';
    } elseif ($size >= 1048576) {
        $fileSize = round($size / 1024 / 1024,1) . 'MB';
    } elseif($size >= 1024) {
        $fileSize = round($size / 1024,1) . 'KB';
    } else {
        $fileSize = $size . ' bytes';
    }
    return $fileSize;
}

function getLinkToSwitchLanguage(string $lang): string {
    $segments = Request::segments();

    $data = [
        Request::root()
    ];

    if (isset($segments[0]) && in_array($segments[0], config('app.locales'))) {
        unset($segments[0]);
    }

    if ($lang !== config('app.defaultLocale')) {
        $data[] = $lang;
    }

    $data = array_merge($data, $segments);

    return implode('/', $data);
}

function isAdmin(): bool
{
    return auth()->user()->position === 'admin';
}
