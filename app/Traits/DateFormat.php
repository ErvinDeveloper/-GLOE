<?php

namespace App\Traits;

use Carbon\Carbon;

trait DateFormat
{
    public function getDateAttribute($value): string
    {
        return (new Carbon($value))->format('d.m.y');
    }
}
