<?php
// Generic functions to supplement PHP's standard functions

/**
 * Return the first value in an array.
 */
if(!function_exists('array_value_first')) {
    function array_value_first($arr, $default=NULL) {
        return ($arr && is_array($arr) ? reset($arr) : $default);
    }
}

/**
 * Return the last value in an array.
 */
if(!function_exists('array_value_last')) {
    function array_value_last($arr, $default=NULL) {
        return ($arr && is_array($arr) ? end($arr) : $default);
    }
}

/**
 * Return the requested value from an array, or return the default if the key is not present.
 */
if(!function_exists('array_value_get')) {
    function array_value_get($key, $arr, $default=NULL) {
        return ($arr && is_array($arr) && array_key_exists($key, $arr) ? $arr[$key] : $default);
    }
}
