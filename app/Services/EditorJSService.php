<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EditorJSService
{
    public function upload(Request $request): ?string
    {
        if ($request->file('file')) {
            $folder = date('Y/m');
            return $request->file('file')->storeAs(
                $folder,
                time() . '_' . Str::random(10) . '.' . $request->file('file')->getClientOriginalExtension(),
                'public'
            );
        }

        return null;
    }

    public function isImage(?string $path): bool
    {
        $mimeType = File::mimeType('storage/' . $path);
        return in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }
}
