<?php



namespace SenseioApplication\Component;


class PageStatus extends \SenseioApplication\Component
{



    public function run() {

        $data=$this->repository->getPagesStatusStatistiques();
        $pages=array();

        foreach ($data as $status=>$count) {

            //$key=preg_replace('`^.*? `', '', $status);

            $key=$status;

            /*
            if(!$key) {
                $key='Undefined status';
            }
            */

            if($key) {
                $pages[$key]=$count;
            }

            arsort($pages);
        }


        return json_encode($pages);
    }




    public function render($width=400, $height=400) {
        $buffer=parent::render();
        return $buffer."\n".
        '<div class="component">'.
            '<h4>Status des pages</h4>'.
            '<div data-className="PageStatus" class="senseio crawlerSpeed" style="width: '.$width.'px; height: '.$height.'px;">
                <meta name="serviceURL" value="http://127.0.0.1/Senseio/public/component/pageStatus"/>
            </div>'.
        '</div>';
    }





}