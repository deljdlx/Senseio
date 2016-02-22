<?php



namespace Senseio;


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





		if($recursive) {


            $links=$this->startPage->getLinks();




            if(!$this->storage->isPageCrawled($this->startPage)) {
                foreach ($links as $link) {
                    if($link->to()->getURL()!=$this->startURL) {



                        if(!$this->storage->linkExists($link)) {
                            $this->storage->saveLink($link);
                        }



                        if($link->isInternal()) {

                            if(!$this->storage->pageExists($link->to())) {
                                $this->storage->lockPage($link->to());
                                $this->storage->savePage($link->to());
                            }
                        }
                    }
                }
                $this->storage->pageCrawled($this->startPage);
            }
		}






	}


}










