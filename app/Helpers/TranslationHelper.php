<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Request;

class TranslationHelper
{
    public static function translateIfNeeded($text)
    {
        $locale = session('locale') ?? Request::cookie('locale', 'en');
        // $locale = session('locale') ?? 'en';
        if ($locale === 'en') {
            return $text;
        }
        $cacheKey = "translation_{$locale}_" . md5($text);
        return Cache::remember($cacheKey, 43200, function () use ($text, $locale) {
            return GoogleTranslate::trans($text, $locale);
        });
    }
}
