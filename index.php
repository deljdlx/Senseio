<?php
chdir(__DIR__);
require('bootstrap.php');









$router=new \Phi\Router();

$router->get('`/component/(?P<component>.*?)$`', function($component) {

    $className='\Senseio\Component\\'.$component;

    if(class_exists($className)) {
        $component=new $className();
        header('Content-type: application/json');
        echo $component->run();
        exit();
    }
});

$router->run();


