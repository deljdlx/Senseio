<?php



namespace SenseioApplication\Configuration;


use Phi\Exception;
use Phi\Storage\Memcache;

class Datasource
{


	protected $sources=array();



	public function __construct() {


		$driver=new \MongoDB\Client('mongodb://localhost');
		$database=$driver->selectDatabase('cosmopolitan');
		$this->sources['crawl']=$database;




		$this->sources['crawlLock']=$database;


		/*
		$memcache=new Memcache();
		$memcache->connect('127.0.0.1');
		$this->sources['crawlLock']=$memcache;
		*/


	}

	public function getSources() {
		return $this->sources;
	}


	public function getSource($sourceName) {
		if(isset($this->sources[$sourceName])) {
			return $this->sources[$sourceName];
		}
		else {
			throw new Exception('Data source "'.$sourceName.'" does not exist');
		}
	}





}





