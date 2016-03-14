<?php


require(__DIR__.'/vendor/autoload.php');
require(__DIR__.'/vendor/Phi/bootstrap.php');





registerNamespace('Senseio', __DIR__.'/vendor/Senseio/class');
registerNamespace('SenseioApplication', __DIR__.'/source');



$application=new \SenseioApplication\Application(__DIR__);
$datasources=new \SenseioApplication\Configuration\Datasource();
$application->setDatasources($datasources);


