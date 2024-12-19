<?php

/**
 * @param array<int, mixed> $array
 */
function getPrevArrayKey(array $array, mixed $needle): mixed
{
    $keys = array_keys($array);
    $index = array_search($needle, $keys);

    return $index ? $keys[$index - 1] : false;
}
