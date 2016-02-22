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
		$this->pages->UpdateMany(array(
			'url'=>$page->getURL()
		),
			array(
				'$set'=>array(
					'crawled'=>true
				),
			)
		);
	}

    public function isPageCrawled($page) {
        $item=$this->pages->findOne(array(
            'url'=>$page->getURL(),
            'crawled'=>true
        ));
        if($item) {
            return true;
        }
        else {
            return false;
        }
    }




	public function savePage($page, $testExists=false) {

		if($testExists && !$this->exists($page)) {
			return false;
		}

		$data=$page->getData();

		$data['crawled']=false;


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



        try {
            $this->links->insertOne(
                array(
                    'from'=>$link->from()->getURL(),
                    'to'=>$link->to()->getURL(),
                    'caption'=>$link->getCaption(),
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

