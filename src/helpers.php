<?php

if (! function_exists('camel_to_dash')) {
    function camel_to_dash($string) {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $string));
    }
}

if (! function_exists('snake_to_camel')) {
    function snake_to_camel($string) {
        return implode('', array_map(function ($item) {
            return ucfirst(strtolower($item));
        }, explode('_', $string)));
    }
}

if (! function_exists('content_to_excerpt')) {
    function content_to_excerpt($string, $length) {
        $string = mb_substr(strip_tags($string), 0, $length);

        if (strlen($string) > $length) {
            return "{$string}...";
        }

        return $string;
    }
}
