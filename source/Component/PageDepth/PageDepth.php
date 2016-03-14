<?php



namespace SenseioApplication\Component;


class PageDepth extends \SenseioApplication\Component
{



    public function run() {

        $data=$this->repository->getPagesDepthStatistiques();

        $pages=array();

        ksort($data);



        $sum=0;

        foreach ($data as $depth=>$count) {
            if($depth) {
                $sum+=$count;
            }
        }

        foreach ($data as $depth=>$count) {

            if($depth) {
                $key="Lvl ".$depth;
                $pages[$key]=round(($count/$sum*100),2);
            }
        }







        return json_encode($pages);
    }




    public function render($width=400, $height=400) {
        $buffer=parent::render();
        return $buffer."\n".
        //'<div class="senseio crawlerSpeed" style="width: '.$width.'px; height: '.$height.'px; border: solid 1px #A00"></div>'.
        '<div data-className="PageDepth" class="senseio crawlerSpeed" style="width: '.$width.'px; height: '.$height.'px; border: solid 1px #A00">
            <meta name="serviceURL" value="http://127.0.0.1/Senseio/public/component/pageDepth"/>
            hello world
        </div>'
            ;
    }





}