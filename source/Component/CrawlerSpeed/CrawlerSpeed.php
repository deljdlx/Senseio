<?php



namespace SenseioApplication\Component;


class CrawlerSpeed extends \SenseioApplication\Component
{



    public function run() {

        //$pageStorage=new \Senseio\MongoStorage($this->repository);

        //$data=$pageStorage->watch();

        return json_encode($this->repository->watchCrawl());
    }



    public function render($width=400, $height=400) {
        $buffer=parent::render();
        return $buffer."\n".

        '<div data-className="CrawlerSpeed" class="senseio crawlerSpeed" style="width: '.$width.'px; height: '.$height.'px; border: solid 1px #A00">
            <meta name="serviceURL" value="http://127.0.0.1/Senseio/public/component/crawlerSpeed"/>
        </div>'
            ;
    }



}