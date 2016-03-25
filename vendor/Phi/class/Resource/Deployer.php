<?php

namespace Phi\Resource;


class Deployer
{


	protected $path;
	protected $url;


	public function __construct($path, $url) {
		$this->path=$path;
		$this->url=$url;
	}


	public function deploy(Resource $resource) {

		if($resource->isLocal()) {
			$fileName=basename($resource->getURI());
			$resourcePath=$resource->getPath();
			copy(
				$resourcePath,
				$this->path.'/'.$fileName
			);
		}



		elseif($resource->isString()) {
			$content=$resource->getContent();

			if($resource instanceof \Phi\Resource\Javascript) {
				$fileName=md5($content).'.js';
			}
			elseif($resource instanceof \Phi\Resource\CSS) {
				$fileName=md5($content).'.css';
			}

			file_put_contents($this->path.'/'.$fileName, $content);
		}

		return $this->url.'/'.$fileName;

	}





}



