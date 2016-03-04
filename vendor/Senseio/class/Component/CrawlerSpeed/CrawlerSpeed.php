<?php



namespace Senseio\Component;


class CrawlerSpeed extends \Senseio\Component
{



    public function run() {

        $driver=new \MongoDB\Client('mongodb://localhost');
        $database=$driver->selectDatabase('cosmopolitan2');

        $pageStorage=new \Senseio\MongoStorage($database);
        $data=$pageStorage->watch();

        return json_encode($data);
    }



    public function render($width=400, $height=400) {
        $buffer=parent::render();
        return $buffer."\n".
        '<div class="senseio crawlerSpeed" style="width: '.$width.'px; height: '.$height.'px; border: solid 1px #A00"></div>'.
        '<div class="senseio crawlerSpeed" style="width: '.$width.'px; height: '.$height.'px; border: solid 1px #A00"></div>'
            ;
    }



}