<?php



require(__DIR__.'/vendor/autoload.php');

require(__DIR__.'/vendor/Phi/bootstrap.php');



registerNamespace('Senseio', __DIR__.'/vendor/Senseio/class');








$logger=new \SenSeio\Logger();


$driver=new MongoDB\Client('mongodb://localhost');
$database=$driver->selectDatabase('test');





$pageStorage=new \Senseio\MongoStorage($database);

$pageStorage->setLogger($logger);




//die('EXIT '.__FILE__.'@'.__LINE__);

if(in_array('--reset', $argv)) {
	$pageStorage->drop();
	$pageStorage->initialize();
}


if(in_array('--watch', $argv)) {
	echo "\n";

	$lastNumberPage=0;
	$lastNumberLinks=0;

	$start=microtime(true);
	while(1) {

		sleep(1);

		$data=$pageStorage->watch();




		if($lastNumberPage) {
			$duration=microtime(true)-$start;

			$insertedPage=$data['pages']-$lastNumberPage;

			$insertedLink=$data['links']-$lastNumberLinks;


			$averagePage=round($insertedPage/$duration, 2);
			$averageLink=round($insertedLink/$duration, 2);


			echo "\rPages : ".$data['pages']."\t\t(".$averagePage.")\t\t\tLinks : ".$data['links']."\t\t".$averageLink;

		}
		else {
			$lastNumberPage=$data['pages'];
			$lastNumberLinks=$data['links'];
		}




	}

}


$url='http://www.cosmopolitan.fr';
//$url='http://www.cosmopolitan.fr/plan.php';


//print_r($pageStorage->getAll());
//exit();


$crawler=new \SenSeio\Crawler($pageStorage, $url);
$crawler->setLogger($logger);
$crawler->run();





$notCrawledPage=$pageStorage->getOneNotCrawledPage();

do {

	echo "============".$notCrawledPage->url."====================\n";

	$crawler=new \SenSeio\Crawler($pageStorage, $notCrawledPage->url);
	$crawler->setLogger($logger);
	$crawler->run(null, true, true);
} while($notCrawledPage=$pageStorage->getOneNotCrawledPage());










