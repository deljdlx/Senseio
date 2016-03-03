<?php


namespace Phi;


use Phi\Exception;

class Route
{

    protected $validator;
    protected $callback;
    protected $verbs=array();

    protected $parameters=array();


    public function __construct($verbs, $validator, $callback) {
        $this->validator=$validator;
        $this->callback=$callback;
        $this->verbs=array($verbs);
    }


    public function validate() {


        $callString=$_SERVER['REQUEST_URI'];


        if(is_string($this->validator)) {
            $matches=array();
            if(preg_match_all($this->validator, $callString, $matches)) {

                if(!empty($matches)) {
                    array_shift($matches);

                    foreach ($matches as $key=>$match) {
                        $this->parameters[$key]=$match[0];
                    }

                }
                return true;
            }
        }


        return false;
    }


    public function getBindedParametersWithMethod($userParameters, $controllerName, $method) {
        $reflector=new \ReflectionMethod($controllerName, $method);
        $parameters=$reflector->getParameters();


        $callParameters=array();

        foreach ($parameters as $parameter) {

            if(isset($userParameters[$parameter->name])) {
                $callParameters[]=$userParameters[$parameter->name];
            }
            else if($parameter->isOptional()) {
                $callParameters[]=$parameter->getDefaultValue();
            }
            else {
                throw new Exception('Method '.$controllerName.'::'.$method.' missing parameter '.$parameter->name);
            }
        }

        return $callParameters;

    }

    public function execute() {

        $reflector=new \ReflectionFunction($this->callback);
        $parameters=$reflector->getParameters();


        $callParameters=array();

        foreach ($parameters as $index=>$parameter) {

            if(isset($this->parameters[$parameter->name])) {
                $callParameters[]=$this->parameters[$parameter->name];
            }
            else if(isset($this->parameters[$index])) {
                $callParameters[]=$this->parameters[$index];
            }
            else if($parameter->isOptional()) {
                $callParameters[]=$parameter->getDefaultValue();
            }
            else {
                throw new Exception('Route callback missing parameter : '.$parameter->name);
            }
        }


        $callback=$this->callback->bindTo($this, $this);

        return call_user_func_array(
            array($callback, '__invoke'),
            $callParameters
        );


    }

    public function getParameters() {
        return $this->parameters;
    }


}
