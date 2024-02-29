<?php

namespace App\Exceptions\EditorJS;

use Exception;
use Illuminate\Support\Facades\Log;

class BlockNotFoundException extends Exception
{
    public function report()
    {
        Log::debug('Блок не найден');
    }
}
