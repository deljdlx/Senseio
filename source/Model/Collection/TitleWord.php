<?php



namespace SenseioApplication\Model\Collection;


use Phi\Exception;
use Phi\Storage\Memcache;

class TitleWord
{




	protected $storage;

	protected $coupleCollection;
	protected $wordCollection;



	public function __construct($storage) {
		$this->storage=$storage;
		$this->coupleCollection=$this->storage->titleWordCouple;
		$this->wordCollection=$this->storage->titleWord;
		$this->initialize();
	}



	public function getWordCollection() {
		return $this->wordCollection;
	}

	public function getCoupleCollection() {
		return $this->coupleCollection;
	}





	public function initialize() {


		$this->storage->dropCollection('titleWordCouple');
		$this->storage->dropCollection('titleWord');


		$fields=array(
			'word',
			'nextWord',
			'count'
		);


		foreach ($fields as $field) {
			$this->coupleCollection->createIndex(array(
				$field => 1,
			));
		}


		$fields=array(
			'word',
			'association',
			'count'
		);


		foreach ($fields as $field) {
			$this->wordCollection->createIndex(array(
				$field => 1,
			));
		}



	}



	public function save($string) {

		$words=explode(' ', $string);



		foreach ($words as $key=>$word) {
			$this->saveWord($word);
			if(isset($words[$key+1])) {
				$this->saveCouple($word, $words[$key+1]);
			}
		}
	}


	public function saveWord($word) {

		$data=$this->wordCollection->findOne(array(
			'word'=>$word,
		));

		if(!$data) {
			$this->wordCollection->insertOne(
				array(
					'word'=>$word,
					'count'=>1,
					'association'=>0,
				)
			);
		}
		else {
			$this->wordCollection->UpdateOne(
				array(
					'word'=>$word,
				),
				array(
					'$inc'=>array('count'=> 1)
				)
			);
		}


	}

	public function saveCouple($word, $nextWord) {

		if(!$this->coupleExists($word, $nextWord)) {
			$this->coupleCollection->insertOne(
				array(
					'word'=>$word,
					'nextWord'=>$nextWord,
					'count'=>1,
				)
			);

			$this->wordCollection->updateOne(
				array(
					'word'=>$word,
				),
				array(
					'$inc'=>array('association'=> 1)
				)
			);

		}
		else {
			$this->coupleCollection->updateOne(
				array(
					'word'=>$word,
					'nextWord'=>$nextWord,
				),
				array(
					'$inc'=>array('count'=> 1)
				)
			);
		}

		echo $word."\t".$nextWord;
		echo "\n";
	}






	public function coupleExists($word, $nextWord) {

		$data=$this->coupleCollection->findOne(array(
			'word'=>$word,
			'nextWord'=>$nextWord
		));

		if($data) {
			return true;
		}
		else {
			return false;
		}

	}




}
