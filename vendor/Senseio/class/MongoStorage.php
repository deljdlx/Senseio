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



	public function getPageCollection() {

	}



	public function setLogger($logger) {
		$this->logger=$logger;
	}


	public function initialize() {

		$fields=array(
			'url',
			'canonical',
			'status',
			'crawlStatus',
			'depth',
		);


		foreach ($fields as $field) {
			$this->pages->createIndex(array(
				$field => 1,
			));
		}


		$linkFields=array(
			'from',
			'to',
			//'caption',
		);


		foreach ($linkFields as $field) {
			$this->links->createIndex(array(
				$field => 1,
			));
		}

	}

	public function drop() {
		$this->database->dropCollection('page');
		$this->database->dropCollection('link');
	}



	public function pageCrawled($page) {
		$this->pages->UpdateMany(array(
			'url'=>$page->getURL()
		),
			array(
				'$set'=>array(
					'crawlStatus'=>Page::STATUS_CRAWLED
				),
			)
		);


		if($this->logger) {
			$this->logger->notice('PAGE CRAWLED' ."\t\t".$page->getURL());
		}
	}

	public function pageLockCrawl($page) {

		if($page instanceof Page) {
			$url=$page->getURL();
		}
		else {
			$url=$page;
		}


		$this->pages->UpdateMany(array(
			'url'=>$url
		),
			array(
				'$set'=>array(
					'crawlStatus'=>Page::STATUS_CRAWLING
				),
			)
		);


		if($this->logger) {
			$this->logger->notice('PAGE CRAWLING' ."\t\t".$url);
		}
	}


    public function isPageCrawled($page) {
        $item=$this->pages->findOne(array(
            'url'=>$page->getURL(),
            'crawlStatus'=>Page::STATUS_CRAWLED
        ), array('projection'=>array('crawlStatus'=>1)));
        if($item) {
            return true;
        }
        else {
            return false;
        }
    }


	public function isPageCrawlable($page) {
		$item=$this->pages->findOne(array(
			'url'=>$page->getURL(),
			),
			array('projection'=>array('crawlStatus'=>1))
		);


		if($item->crawlStatus==Page::STATUS_CRAWLING || $item->crawlStatus==Page::STATUS_CRAWLED) {
			return false;
		}
		else {
			return true;
		}
	}





	public function savePage($page, $testExists=false) {


		if($testExists && !$this->exists($page)) {
			return false;
		}

		$data=$page->getData();


		$data['crawlStatus']=Page::STATUS_CREATED;


        try {
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
                $this->logger->notice('PAGE INSERT' ."\t\t".round($page->getLoadingTime(),3)."\t\t".$page->getStatusCode()."\t".$page->getURL());
            }
        }
        catch(\Exception $e) {
            $this->logger->notice('PAGE INSERT FAILD' ."\t\t".$page->getURL());
        }
	}

	public function savePages($pages) {


		$list=array();

		foreach ($pages as $page) {
			$start=microtime(true);
			$data=$page->getData();
			$load=microtime(true)-$start;
			if($this->logger) {
					$this->logger->notice('PAGE DOWNLOAD' ."\t\t".round($load,3)."\t\t".$page->getStatusCode()."\t".$page->getURL());
			}

			$data['crawlStatus']=Page::STATUS_CREATED;
			$list[]=$data;
		}


		try {
			$this->pages->insertMany($list);

			if($this->logger) {
				foreach ($pages as $page) {
					$this->logger->notice('PAGE INSERT' ."\t\t".round($page->getLoadingTime(),3)."\t\t".$page->getStatusCode()."\t".$page->getURL());
				}
			}
		}
		catch(\Exception $e) {

			if($this->logger) {
				foreach ($pages as $page) {
					$this->logger->notice('PAGE INSERT' ."\t\t".round($page->getLoadingTime(),3)."\t\t".$page->getStatusCode()."\t".$page->getURL());
				}
			}


		}




	}




	public function watch() {

		$crawledPages=$this->pages->count(array('crawlStatus'=>Page::STATUS_CRAWLED));
		$pages=$this->pages->count(array());
		$links=$this->links->count(array());


		return array(
			'crawledPages'=>$crawledPages,
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
			$item=$this->pages->findOne(array('url'=>$page), array('projection'=>array('crawlStatus'=>1)));
		}
		else {
			$item=$this->pages->findOne(array('url'=>$page->getURL()), array('projection'=>array('crawlStatus'=>1)));
		}

		if($item) {
			return true;
		}
		else {
			return false;
		}
	}


    public function linkExists($link) {
        $item=$this->links->findOne(array(
            'from'=>$link->from()->getURL(),
            'to'=>$link->to()->getURL(),
            'caption'=>$link->getCaption(),
        ));

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

	public function findPage($query)
	{
		$list = $this->pages->find($query);
		$items = array();
		foreach ($list as $item) {
			$items[] = $item;
		}
		return $items;
	}


	public function getOneNotCrawledPage($lock=false) {

		$start=microtime(true);
		$list=$this->pages->find(
			array('crawlStatus'=>Page::STATUS_CREATED),
			array(
				array('projection'=>array(
					'depth'=>1,
					'content'=>1,
					'url'=>1,
					'crawlStatus'=>1,
				)),
				'sort'=>array('depth'=>1),
				'limit'=>1
			)
		);




		foreach ($list as $item) {

			$duration=microtime(true)-$start;
			if($this->logger) {
				$this->logger->notice('Retrieving page in '.round($duration, 4).'s'."\t\t".$item->url."\t\t".$item->crawlStatus);
			}
			if($lock) {
				$this->pageLockCrawl($item->url);
			}

			return $item;
		}
	}




	public function getNotCrawledPages( $number=10, $asc=1) {


		$start=microtime(true);

		$list=$this->pages->find(
			array('crawlStatus'=>Page::STATUS_CREATED),
			array(
				array('projection'=>array(
					'depth'=>$asc,
					'content'=>1,
					'url'=>1,
					'crawlStatus'=>1,
				)),
				'sort'=>array('depth'=>1),
				'limit'=>$number
			)
		);




		$items=array();

		$urlList=array();

		foreach ($list as $item) {
			$urlList[]=$item->url;
			$items[]=$item;
		}



		$this->pages->UpdateMany(array(
				'url'=>array('$in'=>$urlList)
			),
			array(
				'$set'=>array(
					'crawlStatus'=>Page::STATUS_CRAWLING
				),
			)
		);
		$duration=microtime(true)-$start;


		if($this->logger) {
			$this->logger->notice('Retrieving '.$number.' page in '.round($duration, 4).'s');
		}


		return $items;
	}












	public function saveLink(Link $link) {



        try {
            $this->links->insertOne(
                array(
                    'from'=>$link->from()->getURL(),
                    'to'=>$link->to()->getURL(),
                    'caption'=>$link->getCaption(),
	                'isInternal'=>$link->isInternal(),
                )
            );

            if($this->logger) {
                $this->logger->notice('LINK INSERT' ."\t\t".$link->from()->getURL()."\t\t".$link->getCaption());
            }
        }
        catch(\Exception $exception) {
                $this->logger->notice('LINK INSERT FAILED' ."\t\t".$link->from()->getURL()."\t\t"."\t\t".$link->from()->getURL().$link->getCaption());
        }


	}





}

