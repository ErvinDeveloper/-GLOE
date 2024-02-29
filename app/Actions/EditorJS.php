<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EditorJS
{
    private array $data;
    private const DIRECTORY_VIEWS = 'editorjs';

    public function set(string $body): static
    {
        $this->data = json_decode($body, true);
        return $this;
    }

    public function render(): ?string
    {
        if (empty($this->data['blocks'])) {
            return null;
        }

        return collect($this->data['blocks'])
            ->reduce(function ($html, $block) {
                try {
                    $viewPath = self::DIRECTORY_VIEWS . '.' . $block['type'];
                    return $html . view($viewPath, compact('block'))->render();
                } catch (\Exception $e) {
                    Log::error("The block with the view \"$viewPath\" was not found in the EditorJS handler\nURL: " . url()->current());
                    return $html;
                }
            });
    }

    public function excerpt(int $limitWords = 5): ?string
    {
        if (empty($this->data['blocks'])) {
            return null;
        }

        $paragraph = collect($this->data['blocks'])->first(function ($block) use ($limitWords) {
            return $block['type'] === 'paragraph';
        });

        if ($paragraph) {
            return Str::of($paragraph['data']['text'])->words($limitWords);
        }

        return null;

    }

}
