<?php



namespace SenseioApplication\Model\Collection;


use Phi\Exception;
use Phi\Storage\Memcache;

class URL
{




	protected $storage;


	public function __construct($storage) {
		$this->storage=$storage;
		$this->collection=$this->storage->url;
		$this->initialize();
	}


	public function initialize() {


		$this->storage->dropCollection('url');

		$fields=array(
			'url',
			'depth',
			'parent'
		);


		foreach ($fields as $field) {
			$this->collection->createIndex(array(
				$field => 1,
			));
		}


	}



	public function save($url) {
		if(!$url instanceof \SenseioApplication\Model\Entity\URL) {
			$url=new \SenseioApplication\Model\Entity\URL($url);
		}



		$parent=$url->getParentURL();


		$exists=false;

		do {
			if(!$this->exists((string) $url) && !$exists) {

				$parentURL=false;
				if($parent) {
					$parentURL=(string) $parent;
				}

				//enregistrement de l'url
				$this->collection->insertOne(
					array(
						'url'=>(string) $url,
						'depth'=>$url->getDepth(),
						'parent'=>$parentURL,
						'childs'=>0
					)
				);

				$parent=$url->getParentURL();
				echo (string) $url."\t =>\t".(string) $url->getParentURL();
				echo "\n";
			}
			else {
				$exists=true;
				$this->collection->UpdateOne(
					array(
						'url'=>(string) $url,
					),
					array(
						'$inc'=>array('childs'=> 1)
					)
				);

			}
		} while($url=$url->getParentURL());

		echo "===============================================";
		echo "\n";







	}





	public function exists($url) {

		$data=$this->collection->findOne(array(
			'url'=>$url
		));

		if($data) {
			return true;
		}
		else {
			return false;
		}

	}




}
