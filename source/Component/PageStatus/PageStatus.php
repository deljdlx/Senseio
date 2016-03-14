<?php



namespace SenseioApplication\Component;


class PageStatus extends \SenseioApplication\Component
{



    public function run() {

        $data=$this->repository->getPagesStatusStatistiques();

        $pages=array();
        foreach ($data as $status=>$count) {

            $key=preg_replace('`^.*? `', '', $status);

            $pages[$key]=$count;
        }


        return json_encode($pages);
    }




    public function render($width=400, $height=400) {
        $buffer=parent::render();
        return $buffer."\n".
        //'<div class="senseio crawlerSpeed" style="width: '.$width.'px; height: '.$height.'px; border: solid 1px #A00"></div>'.
        '<div data-className="PageStatus" class="senseio crawlerSpeed" style="width: '.$width.'px; height: '.$height.'px; border: solid 1px #A00">
            <meta name="serviceURL" value="http://127.0.0.1/Senseio/public/component/pageStatus"/>
            hello world
        </div>'
            ;
    }





}