<?php
/**
 * Created by PhpStorm.
 * User: andres
 * Date: 5/16/2016
 * Time: 10:20 a.m.
 */

$base = "../src/";

$folders = [
    'utils',
    'middleware',
    'controllers'

];

foreach ($folders as $folder) {
    foreach (glob($base . "$folder/*.php") as $filename) {
        // error_log($filename);
        require $filename;
    }
}
