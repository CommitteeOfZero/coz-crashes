<?php

function filesizeFormat($bytes) {
    return number_format($bytes/(1024*1024), 2) . " MB";
}