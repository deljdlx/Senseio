<?php



namespace SenseioApplication\Configuration;


use Phi\Exception;
use Phi\Storage\Memcache;


class Crawler
{



	protected $storage;
	protected $logger;



	public function getLogger() {
		if(! $this->logger) {
			$this->logger=new \Senseio\Logger();
		}

		return $this->logger;
	}


	public function getStorage() {

		if(! $this->storage) {
			$database=\SenseioApplication\Application::getInstance()->getDatasource('crawl');
			$this->storage=new \Senseio\MongoStorage($database);

			$logger=new \Senseio\Logger();
			$this->storage->setLogger($logger);
		}



		return $this->storage;

	}

	public function getSiteName() {
		return 'cosmopolitan.fr';
	}


	public function getRootURL() {
		//return 'http://www.capital.fr';
		//return 'http://www.cuisineetvinsdefrance.com';
		return 'http://www.cosmopolitan.fr';
	}



}