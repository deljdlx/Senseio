<?php



namespace Senseio\Component;


class CrawlerSpeed
{



    public function run() {



        $driver=new \MongoDB\Client('mongodb://localhost');
        $database=$driver->selectDatabase('cosmopolitan2');


        $pageStorage=new \Senseio\MongoStorage($database);
        $data=$pageStorage->watch();


        return json_encode($data);


        return 'hello world';
    }

}