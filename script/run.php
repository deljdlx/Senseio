<?php


chdir(__DIR__.'/..');

require(__DIR__.'/../vendor/autoload.php');

require(__DIR__.'/../vendor/Phi/bootstrap.php');



registerNamespace('Senseio',realpath( __DIR__.'/../vendor/Senseio/class'));








$logger=new \SenSeio\Logger();


$driver=new MongoDB\Client('mongodb://localhost');
$database=$driver->selectDatabase('cosmopolitan2');





$pageStorage=new \Senseio\MongoStorage($database);

$pageStorage->setLogger($logger);



//$pageStorage->getOneNotCrawledPage();
//die('EXIT '.__FILE__.'@'.__LINE__);


//die('EXIT '.__FILE__.'@'.__LINE__);

if(in_array('--reset', $argv)) {
	$pageStorage->drop();
	$pageStorage->initialize();
	exit();
}


if(in_array('--watch', $argv)) {
	echo "\n";

	$lastNumberPage=0;
	$lastNumberLinks=0;

	$start=microtime(true);


	while(1) {

		sleep(2);

		$data=$pageStorage->watch();




		if($lastNumberPage) {
			$duration=microtime(true)-$start;

			$insertedPage=$data['pages']-$lastNumberPage;

			$insertedLink=$data['links']-$lastNumberLinks;


			$averagePage=round($insertedPage/$duration, 2);
			$averageLink=round($insertedLink/$duration, 2);


			echo "\r".round($duration,2)."\t\t\t"."Pages : ".$data['crawledPages'].'/'.$data['pages']."\t\t(".$averagePage.")\t\t\tLinks : ".$data['links']."\t\t".$averageLink;


			$pageStorage->setLogger(null);
			$notCrawledPage=$pageStorage->getOneNotCrawledPage();
			if(!$notCrawledPage) {
				echo "\n";
				//exit();
			}

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



//$list=$pageStorage->getNotCrawledPages();
//print_r($list);
//die('EXIT '.__FILE__.'@'.__LINE__);


$crawler=new \SenSeio\Crawler($pageStorage, $url);
$crawler->setLogger($logger);
$crawler->run();



$asc=1;

$notCrawledPages=$pageStorage->getNotCrawledPages($asc);
do {

	foreach ($notCrawledPages as $notCrawledPage) {

		$page=new \Senseio\Page($notCrawledPage->url, $notCrawledPage->depth);
		$page->setContent($notCrawledPage->content);

		$crawler=new \Senseio\Crawler($pageStorage, $page, $notCrawledPage->depth);
		$crawler->setLogger($logger);
		$crawler->run(null, true, true, true);
	}



	//$asc=$asc*-1;


} while($notCrawledPages=$pageStorage->getNotCrawledPages($asc));







/*
while(true)  {
	$notCrawledPage=$pageStorage->getOneNotCrawledPage(true);

	if($notCrawledPage) {
		do {

			$page=new \Senseio\Page($notCrawledPage->url, $notCrawledPage->depth);
			$page->setContent($notCrawledPage->content);

			$crawler=new \Senseio\Crawler($pageStorage, $page, $notCrawledPage->depth);
			$crawler->setLogger($logger);
			$crawler->run(null, true, true, true);
		} while($notCrawledPage=$pageStorage->getOneNotCrawledPage());
	}
	else {

		$data=$pageStorage->watch();
		if($data['pages']<=1) {
			sleep(1);
			echo "WAITING NEW PAGES\t".$data['pages']."\n";
		}
	}
}
*/










