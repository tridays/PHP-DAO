<?php

function get_in_array_or_default(&$arr, $key, $default = null) {
    if (isset($arr[$key])) {
        return $arr[$key];
    } else {
        return $default;
    }
}