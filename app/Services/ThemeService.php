<?php

namespace App\Services;

use App\Http\Requests\Admin\Theme\StoreRequest;
use App\Http\Requests\Admin\Theme\UpdateRequest;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ThemeService
{
    public function store(StoreRequest $request): Model
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);
        return Theme::query()->create($data);
    }

    public function update(UpdateRequest $request, Theme $theme): bool
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        return $theme->update($data);
    }

    public function delete(Theme $theme): ?bool
    {
        return $theme->delete();
    }
}
