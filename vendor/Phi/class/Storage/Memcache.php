<?php
namespace Phi\Storage;



class Memcache extends \Memcache
{



	public function set($name, $value) {
		$this->add($name, $value);
		return $this;
	}



}