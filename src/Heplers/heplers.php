<?php

//TODO переделать под стак
function getPrevArrayKey($array, $needle): string|false
{
    $keys = array_keys($array);
    $index = array_search($needle, $keys);
    return $index ? $keys[$index - 1] : false;
}
