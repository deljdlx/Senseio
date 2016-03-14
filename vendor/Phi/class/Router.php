<?php

namespace Phi;


class Router implements \Phi\Interfaces\Router
{


    protected $routes=array();
    protected $routesByName=array();


    public function addRoute(Route $route, $name='') {
        $this->routes[]=$route;
        $this->routesByName[$name]=$route;
        return $route;
    }


    public function get($validator, $callback, $name='') {
        return $this->addRoute(
            new Route('get', $validator, $callback),
            $name
        );
    }

    public function run() {

        foreach ($this->routes as $route) {

            if($route->validate()) {
                return $route->execute();
            }
        }
    }
}





