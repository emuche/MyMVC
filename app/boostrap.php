<?php
require_once 'config/config.php';
spl_autoload_register(function($className){

    $sources = [
        APPROOT."/libraries/$className.php",
        APPROOT."/helpers/$className.php",
    ];

    foreach($sources as $source){
        if(file_exists($source)){
            require_once $source;
        }
    }
});