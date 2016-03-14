<?php
chdir(__DIR__);
require('bootstrap.php');





//$configuration=new \SenseioApplication\Configuration\Datasource();
//$test=new \SenseioApplication\Component();





$router=new \Phi\Router();
$application->setRouter($router);



$application->get('`/component/(?P<component>.*?)$`', function($component) use ($application) {

    $className='\SenseioApplication\Component\\'.$component;

    if(class_exists($className)) {


        $dataSource=$application->getDatasource('crawl');
        $repository=new \SenseioApplication\Model\Repository($dataSource);

        $componentInstance=new $className($repository);

        $output=$componentInstance->run();

        header('Content-type: application/json');
        echo $output;
        exit();
    }
});





$application->run();



include('source/page/test.php');


//die('EXIT '.__FILE__.'@'.__LINE__);





