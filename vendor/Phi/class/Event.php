<?php
namespace Phi;


class Event
{

    public $name='event';
    public $data;
    public $source;

    protected $defaultPrevented=false;


    public function __construct($name='event', $data=array(), $source=null) {
        $this->name=$name;
        $this->data=$data;
        $this->source=$source;
    }

    public function getSource() {
        return $this->source;
    }

    public function getData() {
        return $this->data;
    }

    public function preventDefault() {
        $this->defaultPrevented=true;
    }

    public function isDefaultPrevented() {
        return $this->defaultPrevented;
    }



}