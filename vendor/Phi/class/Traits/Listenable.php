<?php
namespace Phi\Traits;


use Phi\Event;

Trait Listenable
{


    protected $listeners=array();
    protected $defaultListeners=array();


    public function getDefaultListeners() {
        return $this->defaultListeners;
    }



    public function addDefaultEventListener($name, $listener) {
        if(!isset($this->defaultListeners[$name])) {
            $this->defaultListeners[$name]=array();
        }
        $this->defaultListeners[$name][]=$listener;
        return $this;
    }



    public function addEventListener($eventName, $listener, $name=null) {

        $normalizedEventName=strtolower($eventName);

        if(!isset($this->listeners[$normalizedEventName])) {
            $this->listeners[$normalizedEventName]=array();
        }
        if($name) {
            $this->listeners[$normalizedEventName][$name]=$listener;
        }
        else {
            $this->listeners[$normalizedEventName][]=$listener;
        }
        return $this;
    }




    public function fireEvent($eventName, $data=array()) {

        $normalizedEventName=strtolower($eventName);

        if(!is_array($data)) {
            $data=array($data);
        }

        $event=new Event($eventName, $data, $this);
        $data[]=$event;


        if(isset($this->listeners[$normalizedEventName])) {
            foreach ($this->listeners[$normalizedEventName] as $listener) {
                if(is_callable($listener)) {
                    $bindedClosure=$listener->bindTo($this);
                    call_user_func_array(array($bindedClosure, '__invoke'), $data);
                }
            }
        }

        if(!$event->isDefaultPrevented()) {
            if(isset($this->defaultListeners[$normalizedEventName])) {
                foreach ($this->defaultListeners[$normalizedEventName] as $listener) {
                    if(is_callable($listener)) {

                        $bindedClosure=$listener->bindTo($this);
                        call_user_func_array(array($bindedClosure, '__invoke'), $data);
                    }
                }
            }
        }

    }
}



