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


$collection=new \SenseioApplication\Model\Collection\TitleWord($database);

foreach ($data as $values) {

	$clean=$values->title;

	$clean=removeAccent($clean);

	$clean=mb_strtolower($clean);

	$clean=preg_replace('`\b\w{1,2}\b`u', ' ', $clean);
	$clean=preg_replace('`\W`u', ' ', $clean);
	$clean=preg_replace('`\W+`u', ' ', $clean);
	$clean=trim($clean);

	$collection->save($clean);
}





