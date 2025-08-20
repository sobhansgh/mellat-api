<?php

if (! function_exists('rial')) {
    function rial(int $toman): int
    {
        return $toman * 10; // Mellat expects Rial
    }
}
