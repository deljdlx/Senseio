<?php



namespace Senseio;
use SenseioApplication\Application;


/**
 * Class Crawler
 * @property MongoStorage $storage
 * @package Senseio
 */


class Crawler
{



	protected $configuration;

	protected $startURL;
	protected $startPage;

	protected $storage;


	protected $logger;


	protected $crawledPages=array();

	protected $depth;


	protected $lockEngine;



	public function __construct($crawlerConfiguration, $startURL=null, $depth=0) {


		$this->configuration=$crawlerConfiguration;

		if(!$startURL) {
			$startURL=$this->configuration->getRootURL();
		}



		if($startURL instanceof Page) {
			$this->startPage=$startURL;
			$this->startURL=$startURL->getURL();
		}
		else {
			$this->startURL=$startURL;
		}

		$this->depth=$depth;
		$this->storage=$this->configuration->getStorage();


		$this->setLogger($this->configuration->getLogger());



		$this->storage->addPageTitleFilter(function($pageData) {
			$pageData['normalizedTitle']=str_ireplace($this->configuration->getSiteName(), '', $pageData['normalizedTitle']);
			$pageData['normalizedTitle']=$this->normalizePageTitle($pageData['normalizedTitle']);
			return $pageData;

		});

	}





	public function normalizePageTitle($string) {

		$string=mb_strtolower($string);

		$string=str_replace("'", " ", $string);
		$string=str_replace("’", " ", $string);
		$string=preg_replace('`\W`u', " ", $string);


		$string=removeAccent($string);

		$string=preg_replace('`\s+`', ' ', $string);
		$string=trim($string);

		return $string;

	}






	public function getLockEngine() {
		if($this->lockEngine===null) {
			$this->lockEngine=Application::getInstance()->getDatasource('crawlLock');
		}
		return $this->lockEngine;
	}


	public function setLockEngine($engine) {
		$this->lockEngine=$engine;
		return $this;
	}





	public function setLogger($logger) {
		$this->logger=$logger;
	}



	public function run($page=null, $recursive=true, $skipInsert=false, $skipControl=false) {

		if($page) {
			if(is_string($page)) {
				$this->startURL=$page;
				$this->startPage=new Page($this->startURL, $this->depth);
			}
			else {
				$this->startURL=$page->getURL();
				$this->startPage=$page;
			}
		}
		else if(!$this->startPage instanceof  Page) {
			$this->startPage=new Page($this->startURL, $this->depth);
		}


		if(!$skipInsert) {


			if(!$this->storage->pageExists($this->startURL)) {
				//$this->storage->lockPage($this->startPage);
				$this->storage->savePage($this->startPage);
			}
			else if($this->logger) {
				$this->logger->notice('SKIP'."\t".$this->startPage->getURL());
			}
		}





		if($recursive) {

			if($this->storage->isPageCrawlable($this->startPage) || $skipControl) {


				if(!$skipControl) {
					$this->storage->pageLockCrawl($this->startPage);
				}




                $links=$this->startPage->getLinks();








				$pagesToInsert=array();


                foreach ($links as $link) {
                    if($link->to()->getURL()!=$this->startURL) {



                        //if(!$this->storage->linkExists($link)) {
                            $this->storage->saveLink($link);
                        //}




                        if($link->isInternal()) {

                            if(!$this->storage->pageExists($link->to())) {
	                            //if($this->depth==0) {
		                            $this->storage->lockPage($link->to());
		                            $this->storage->savePage($link->to());
	                            /*
	                            }
	                            else {
		                            $pagesToInsert[]=$link->to();
	                            }
	                            */
                            }
                        }
                    }
                }

				/*
				if(!$this->depth) {
					$this->storage->savePages($pagesToInsert);
				}
				*/




                $this->storage->pageCrawled($this->startPage);
            }
			else if($this->startPage->getURL()!='http://www.cosmopolitan.fr') {
				//die('EXIT '.__FILE__.'@'.__LINE__);
			}
		}






	}


}










