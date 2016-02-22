<?php



namespace SenSeio;




class Crawler
{

	protected $startURL;
	protected $startPage;

	protected $storage;


	protected $logger;


	protected $crawledPages=array();

	public function __construct($storage, $startURL=null) {
		$this->startURL=$startURL;
		$this->storage=$storage;
	}



	public function setLogger($logger) {
		$this->logger=$logger;
	}



	public function run($page=null, $recursive=true, $skipInsert=false) {


		if($page) {
			if(is_string($page)) {
				$this->startURL=$page;
				$this->startPage=new Page($this->startURL);
			}
			else {
				$this->startURL=$page->getURL();
				$this->startPage=$page;
			}
		}
		else {
			$this->startPage=new Page($this->startURL);
		}


		if(!$skipInsert) {
			if(!$this->storage->pageExists($this->startURL)) {
				$this->storage->lockPage($this->startPage);
				$this->storage->savePage($this->startPage);
			}
			else if($this->logger) {
				$this->logger->notice('SKIP'."\t".$this->startPage->getURL());
			}
		}





		$links=$this->startPage->getLinks();


		if($recursive) {

			foreach ($links as $link) {
				if($link->isInternal() && $link->to()->getURL()!=$this->startURL) {
					$this->storage->saveLink($link);


					if(!$this->storage->pageExists($link->to())) {
						$this->storage->lockPage($link->to());
						$this->storage->savePage($link->to());
					}

				}
			}
			$this->storage->pageCrawled($this->startPage);
		}






	}


}










