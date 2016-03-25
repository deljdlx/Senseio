<?php

namespace Phi\Resource;


class Resource
{



	protected $stringURI;


	public function __construct($uri, $parameters=array()) {
		$this->stringURI=$uri;
		$this->parameters=$parameters;
	}


	public function isLocal() {
		if(strpos($this->stringURI, 'file://')===0) {
			return true;
		}
		else {
			return false;
		}
	}

	public function isString() {
		if(strpos($this->stringURI, 'string://')===0) {
			return true;
		}
		else {
			return false;
		}
	}

	public function getURI() {
		return $this->stringURI;
	}

	public function getPath() {
		return str_replace('file://', '', $this->getURI());
	}


	public function getContent() {
		if($this->isString()) {
			return str_replace('string://', '', $this->getURI());
		}
		else {
			return file_get_contents($this->getURI);
		}
	}

}

