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


        echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
        echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
        print_r(uniqid());
        echo '</pre>';


        $data=$this->pages->aggregate(array(
            array(
                '$unwind'=>'$headers',
                '$project'=>array(
                    'length'=>array('$length'=>'$headers.Content-Length')
                ),
                /*
                '$group'=>array(
                    '_id'=>'$test',
                    'average'=>array('$avg'=>'$test')
                )
                */

            )
        ));

        foreach ($data as $value) {
            echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
            echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
            print_r($value);
            echo '</pre>';
        }

        die('EXIT '.__FILE__.'@'.__LINE__);

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








        $data=$this->pages->find(
            array(),
            array(
                'projection'=>array(
                    'headers'=>1
                ),
                'limit'=>2,
            )
        );

        foreach ($data as $value) {
            echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
            echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
            print_r($value);
            echo '</pre>';
        }

        //Content-Length
        die('EXIT '.__FILE__.'@'.__LINE__);



        //=====================================
        $returnValue['general']=array(
            'total'=>$total,
            'averageLoadingTime'=>$loadingTime,
            'averageSize'=>$bufferSize,
        );

        $returnValue['status']=$statistiques;

        return $returnValue;

        die('EXIT '.__FILE__.'@'.__LINE__);
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





