<?php
    spl_autoload_register(function ($class) {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        include_once $path . '.php';
    });