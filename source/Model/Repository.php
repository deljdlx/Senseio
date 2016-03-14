<?php



namespace SenseioApplication\Model;


use Phi\Exception;
use Phi\Storage\Memcache;

class Repository
{






	public function __construct($storage=null) {

		if(!$storage) {
			$this->storage=\SenseioApplication\Application::getInstance()->getDatasource('crawl');
		}
		else {
			$this->storage=$storage;
		}


		$this->pages=$this->storage->page;
		$this->links=$this->storage->link;
	}


	public function watchCrawl() {

		$crawledPages=$this->pages->count(array('crawlStatus'=>Page::STATUS_CRAWLED));
		$pages=$this->pages->count(array());
		$links=$this->links->count(array());

		return array(
			'crawledPages'=>$crawledPages,
			'pages'=>$pages,
			'links'=>$links
		);
	}


	public function getPagesStatusStatistiques() {


		$data=$this->pages->aggregate(array(
			array(
				'$group'=>array(
					'_id'=>array('status'=>'$status'),
					'count'=>array('$sum'=>1)
				)
			)
		));

		$statistiques=array();

		foreach ($data as $value) {


			$key=$value->_id->status;
			$count=$value->count;

			$statistiques[$key]=$count;
		}


		return $statistiques;

	}


	public function getPagesDepthStatistiques() {


		//db.page.aggregate({ $group: {"_id":"$url", "count": {"$sum":1}} },{"$match": {"count"{"$gt":1}}})

		$data=$this->pages->aggregate(array(
			array(
				'$group'=>array(
					'_id'=>array('depth'=>'$depth'),
					'count'=>array('$sum'=>1)
				)
			)
		));

		$statistiques=array();

		foreach ($data as $value) {


			$key=$value->_id->depth;
			$count=$value->count;

			$statistiques[$key]=$count;
		}


		return $statistiques;

	}




}





