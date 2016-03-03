<?php
include(__DIR__.'/class/Autoloader.php');
include(__DIR__.'/helper/string.php');


function registerNamespace($namespace, $folder) {
    static $autoloader;
    static $componentAutoloader;
    if(!$autoloader) {
        $autoloader=new \Phi\Autoloader();
        spl_autoload_register(function($calledClassName) use ($autoloader) {
            $autoloader->autoload($calledClassName);
        });
    }
    $autoloader->addNamespace($namespace, $folder);


    if(!$componentAutoloader) {
        $componentAutoloader=new \Phi\PackageAutoloader();
        spl_autoload_register(function($calledClassName) use ($componentAutoloader) {
            $componentAutoloader->autoload($calledClassName);
        });
    }
    $componentAutoloader->addNamespace($namespace, $folder);



}

registerNamespace('Phi', __DIR__.'/class');












