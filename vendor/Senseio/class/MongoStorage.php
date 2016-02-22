<?php



namespace Senseio;




class MongoStorage
{


	protected $database;
	protected $pages;
	protected $logger;


	public function __construct($database) {
		$this->database=$database;
		$this->pages=$this->database->page;
		$this->links=$this->database->link;

	}



	public function setLogger($logger) {
		$this->logger=$logger;
	}


	public function initialize() {
		$this->pages->createIndex(array(
			'url' => 1,
			'canonical'=>1,
			'status'=>1,
			'crawled'=>1
		));


		$this->links->createIndex(array(
			'from' => 1,
			'to'=>1,
			'caption'=>1
		));

	}

	public function drop() {
		$this->database->dropCollection('page');
		$this->database->dropCollection('link');
	}



	public function pageCrawled($page) {
		$this->pages->findOneAndUpdate(array(
			'url'=>$page->getURL()
		),
			array(
				'$set'=>array(
					'crawled'=>true
				),
			)
		);
	}

	public function savePage($page, $testExists=false) {

		if($testExists && !$this->exists($page)) {
			return false;
		}

		$data=$page->getData();

		$data['crawled']=false;

		$this->pages->findOneAndUpdate(array(
			'url'=>$page->getURL()
			),
			array(
				'$set'=>$data,
			),
			array(
			'upsert'=>true
			)
		);

		if($this->logger) {
			$this->logger->notice(round(memory_get_usage()/1024) ."\t\t".round($page->getLoadingTime(),3)."\t\t".$page->getStatusCode()."\t".$page->getURL());
		}


	}


	public function watch() {

		$pages=$this->pages->count(array());
		$links=$this->links->count(array());


		return array(
			'pages'=>$pages,
			'links'=>$links
		);
	}



	public function lockPage($page) {

		if(is_string($page)) {
			$url=$page;
		}
		else {
			$url=$page->getURL();
		}

		$this->pages->insertOne(array(
			'url'=>$url
		));
	}

	public function pageExists($page) {

		if(is_string($page)) {
			$item=$this->pages->findOne(array('url'=>$page));
		}
		else {
			$item=$this->pages->findOne(array('url'=>$page->getURL()));
		}

		if($item) {
			return true;
		}
		else {
			return false;
		}

	}


	public function getAllPage() {
		$list=$this->pages->find(array());

		$pages=array();
		foreach ($list as $item) {
			$pages[]=$item;
		}
		return $pages;
	}

	public function findPage($query) {
		$list=$this->pages->find($query);
		$items=array();
		foreach ($list as $item) {
			$items[]=$item;
		}
		return $items;
	}

	public function getOneNotCrawledPage() {
		$item=$this->pages->findOne(array(
			'crawled'=>false
		));
		return $item;
	}



	public function saveLink($link) {



		$this->links->insertOne(
			array(
				'from'=>$link->from()->getURL(),
				'to'=>$link->from()->getURL(),
				'caption'=>$link->getCaption(),
			)
		);

	}





}

