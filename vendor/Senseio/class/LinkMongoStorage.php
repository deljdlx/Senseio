<?php



namespace Senseio;




class LinkMongoStorage
{


	protected $database;
	protected $collection;


	public function __construct($database) {
		$this->database=$database;
		$this->collection=$this->database->link;

	}

	public function initialize() {
		$this->collection->createIndex(array(
			'caption' => 1,
			'from'=>1,
			'to'=>1
		));
	}

	public function drop() {
		$this->database->dropCollection('link');
	}


	public function save($page, $testExists=false) {

		if($testExists && !$this->exists($page)) {
			return false;
		}


		$data=$page->getData();

		$this->collection->findOneAndUpdate(array(
			'url'=>$page->getURL()
			),
			array(
				'$set'=>$data,
			),
			array(
			'upsert'=>true
			)
		);
	}




	public function lock($page) {

		if(is_string($page)) {
			$url=$page;
		}
		else {
			$url=$page->getURL();
		}

		$this->collection->insertOne(array(
			'url'=>$url
		));
	}

	public function exists($page) {

		if(is_string($page)) {
			$item=$this->collection->findOne(array('url'=>$page));
		}
		else {
			$item=$this->collection->findOne(array('url'=>$page->getURL()));
		}

		if($item) {
			return true;
		}
		else {
			return false;
		}

	}

}

