<?php



namespace Senseio\Model;


class Repository
{




	public function __construct($host, $database) {

		$this->host=$host;
		$this->database=$database;


		$this->mongoDriver=new \MongoDB\Client('mongodb://'.$host);
		$this->mongoDatabase=$this->mongoDriver->selectDatabase($database);


	}





}






