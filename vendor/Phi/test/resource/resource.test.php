<?php


require(__DIR__.'/../../bootstrap.php');



$manager=new \Phi\Resource\Manager();



$deployer=new \Phi\Resource\Deployer(__DIR__.'/deploy', './deploy');

$manager->setDeployer($deployer);


$jQuery=new \Phi\Resource\Javascript('http://code.jquery.com/jquery-2.2.2.min.js');
$manager->registerResource($jQuery);


$localJs=new \Phi\Resource\Javascript('file://'.__DIR__.'/asset/test.js');
$manager->registerResource($localJs);
$manager->deployResource($localJs);


$localJs2=new \Phi\Resource\Javascript('file://'.__DIR__.'/asset/test2.js');
$manager->registerResource($localJs2);



$manager->merge();
$manager->deployMergedJavascript();



echo $manager->renderHTMLTags();