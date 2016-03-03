<?php


namespace Phi;




class DataSourceManager
{

	protected static $sources=null;


	static public function add($name, $source) {
		static::$sources[$name]=$source;
	}

	static public function get($name) {
		if(isset(static::$sources[$name])) {
			return static::$sources[$name];
		}
        else if(isset(static::$sources['default'])) {
            return static::$sources['default'];
        }
		else {
			throw new Exception('Data source "'.$name.'" not found');
		}
	}

}