<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FileService
{
    public function upload(Model $model, Request $request)
    {

        if (!method_exists($model, 'storage')) {
            return null;
        }

        if (!$request->has('files')) {
            return null;
        }

        $folder = date('Y/m');
        $insert = [];

        $files = $request->file('files');

        if (empty($files))  {
            return null;
        }
        foreach ($files as $key => $file) {
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();
            $originalName = $file->getClientOriginalName();

            $path = $file->storeAs(
                $folder,
                time() . '_' . Str::random(10) . '.' . $extension,
                'public'
            );

            $insert[$key] = [
                'path' =>  $path,
                'extension' => $extension,
                'filesize' => $size,
                'user_id' =>  auth()->user()->id,
                'count_downloads' => 0,
                'original_name' => $originalName
            ];
        }

        return $insert;

    }
}
