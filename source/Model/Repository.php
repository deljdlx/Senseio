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



    public function getGeneralStatistiques() {

        $data=$this->pages->aggregate(array(
	        array(
		        '$match'=>array(
			        'headers.Content-Length'=>array(
				        '$ne'=>0
			        )
		        )
	        ),
            array(
	            '$unwind'=>'$headers'
            ),
	        array(
		        '$project'=>array(
			        'length'=>'$headers.Content-Length',
			        'loadingTime'=>'$loadingTime',
			        //'headers'=>'$headers'
		        )
	        ),
	        array(
		        '$group'=>array(
			        '_id'=>null,
			        'average'=>array(
				        '$avg'=>'$length'
			        )
		        )
	        )
        ));



	    $serverSizeAverage=null;
        foreach ($data as $value) {
	        $serverSizeAverage=$value->average;
        }


        //====================================



        $data=$this->pages->aggregate(array(
            array(
                '$group'=>array(
                    '_id'=>array('status'=>'$status'),
                    'count'=>array('$sum'=>1)
                )
            )
        ));

        $statistiques=array();

        $total=0;
        foreach ($data as $value) {
            $key=$value->_id->status;
            $count=$value->count;
            $statistiques[$key]=$count;

            $total+=$count;
        }


        //===================================
        $data=$this->pages->aggregate(array(
            array(
                '$group'=>array(
                    '_id'=>null,
                    'average'=>array('$avg'=>'$loadingTime')
                )
            )
        ));

        foreach ($data as $value) {
           $loadingTime=$value->average;
        }
        //====================================
        $data=$this->pages->aggregate(array(
            array(
                '$group'=>array(
                    '_id'=>null,
                    'average'=>array('$avg'=>'$bufferSize')
                )
            )
        ));

        foreach ($data as $value) {
            $bufferSize=$value->average;
        }
        //===================================

        $data=$this->pages->aggregate(array(
            array(
                '$group'=>array(
                    '_id'=>null,
                    'average'=>array('$avg'=>'$bufferSize')
                )
            )
        ));

        foreach ($data as $value) {
            $bufferSize=$value->average;
        }

        //====================================


	    $linkCount=$this->links->count();







        //=====================================
        $returnValue['general']=array(
	        'linkCount'=>$linkCount,
            'total'=>$total,
            'averageLoadingTime'=>$loadingTime,
            'averageSize'=>$bufferSize,
	        'serverAverageSize'=>$serverSizeAverage,
        );

        $returnValue['status']=$statistiques;

        return $returnValue;

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





