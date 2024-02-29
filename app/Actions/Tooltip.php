<?php

namespace App\Actions;

use App\Models\Dictionary;

class Tooltip
{
    private ?string $body;
    private EditorJS $editorjs;

    public function __construct()
    {
        $this->editorjs = new EditorJS();
    }

    public function set(?string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function replace(): ?string
    {

        $replaceData = [];
        $dictionary = Dictionary::query()->select('word', 'body')->get();
        $dictionary->each(function ($item) use (&$replaceData) {
            $item->title = $this->editorjs->set($item->body)->render();
            $replaceData[$item->word] = view('tooltip.word', compact('item'));
        });


        return str_replace(array_keys($replaceData), array_values($replaceData), $this->body);

    }

}
