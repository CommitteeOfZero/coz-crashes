<?php

function hex2guid($hex) {
    return strtolower(substr($hex, 0, 8)
        . '-' . substr($hex, 8, 4)
        . '-' . substr($hex, 12, 4)
        . '-' . substr($hex, 16, 4)
        . '-' . substr($hex, 20, 12));
}

function filesizeFormat($bytes) {
    return number_format($bytes/(1024*1024), 2) . " MB";
}