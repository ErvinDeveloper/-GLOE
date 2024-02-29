<?php

namespace App\Services;

use App\Http\Requests\Admin\Dictionary\StoreRequest;
use App\Http\Requests\Admin\Dictionary\UpdateRequest;
use App\Models\Dictionary;
use Illuminate\Database\Eloquent\Model;

class DictionaryService
{
    public function store(StoreRequest $request): Model
    {
        $data = $request->validated();
        return Dictionary::query()->create($data);
    }

    public function update(
        Dictionary    $dictionary,
        UpdateRequest $request
    ): bool
    {
        $data = $request->validated();
        return $dictionary->update($data);
    }

    public function delete(Dictionary $dictionary): bool
    {
        return $dictionary->delete();
    }
}
