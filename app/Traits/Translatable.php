<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;
use LogicException;

trait Translatable
{
    public function __($fieldName)
    {
        $lang = App::getLocale() ?? config('app.defaultLang');

        if ($lang === 'lat') {
            $fieldName .= '_lat';
        }

        $attributeKeys = array_keys($this->attributes);

        if (!in_array($fieldName, $attributeKeys)) {
            throw new LogicException('no such attribute for model' . get_class($this));
        }

        return $this->$fieldName;
    }
}
