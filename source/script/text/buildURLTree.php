<?php


chdir(__DIR__.'/../../..');


require('bootstrap.php');




/**
 * @var \SenseioApplication\Application application
 */
$database=$application::getInstance()->getDatasource('crawl');




$pageStorage=new \Senseio\MongoStorage($database);
$pages=$pageStorage->getPageCollection();


$data=$pages->find(array(

), array(
	'projection'=>array(
		'title'=>1,
		'url'=>1
	)
));


$urlCollection=new \SenseioApplication\Model\Collection\URL($database);


foreach ($data as $values) {
	$url=new \SenseioApplication\Model\Entity\URL($values['url']);
	$urlCollection->save($url);
	//echo $values->url."\t".$values->title."\n";
}






