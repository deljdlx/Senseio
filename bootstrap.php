<?php

chdir(__DIR__);

require(__DIR__.'/vendor/autoload.php');

require(__DIR__.'/vendor/Phi/bootstrap.php');



registerNamespace('Senseio', __DIR__.'/vendor/Senseio/class');





$router=new \Phi\Router();

$router->get('`/component/(?P<component>.*?)$`', function($component) {


	echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
	echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
	print_r($component);
	echo '</pre>';
	echo 'hello world';
	exit();

});

$router->run();
