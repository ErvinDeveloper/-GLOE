<?php

namespace App\Services\Localization;

class Localization
{
    public static function locale(): string {
        $locale =  request()->segment(1, '');

        if (!empty($locale) && in_array($locale, config('app.locales'))) {
            return $locale;
        }

        return '';
    }
}
