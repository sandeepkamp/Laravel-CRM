<?php

namespace App\Helpers;

class MetaHelper
{
    public static function addMetaIfFilled($request, &$metaData, $key)
    {
        if ($request->filled($key) && !empty($request->input($key))) {
            $metaData[$key] = json_encode($request->input($key));
        }
    }
}
