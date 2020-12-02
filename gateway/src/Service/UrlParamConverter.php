<?php

declare(strict_types=1);

namespace App\Service;

class UrlParamConverter
{
    public static function convert($url, $paramName, $paramValue)
    {
        return str_replace('{' . $paramName . '}', $paramValue, $url);
    }
}