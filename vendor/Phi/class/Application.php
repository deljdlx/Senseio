<?php
namespace Phi;




use Phi\Interfaces\Router;

class Application
{



	static protected $instances=array();

	protected $path;


	protected $router;
	protected $datasources;


	/**
	 * @param string $name
	 * @return Application
	 * @throws Exception
	 */
	public static function getInstance($name='main') {

		if(isset(static::$instances[$name])) {
			return static::$instances[$name];
		}
		else {
			throw new Exception('Application instance with name '.$name.' does not exist');
		}
	}


	public function __construct($path, $name='main') {
		$this->path=$path;
		static::$instances[$name]=$this;
	}

	public function run() {

		if($this->router) {
			return $this->router->run();
		}
		else {
			return false;
		}
	}



	public function setDatasources($sources) {
		$this->datasources=$sources;
		return $this;
	}

	public function getDatasource($name) {
		return $this->datasources->getSource($name);
	}




	public function setRouter(Router $router) {
		$this->router=$router;
		return $this;
	}






	public function initializeRouter() {
		$this->router=new \Phi\Router();
	}


	public function get($route, $callback) {

		if(!$this->router instanceof Router) {
			$this->initializeRouter();
		}

		$this->router->get($route, $callback);

		return $this;

	}




}
