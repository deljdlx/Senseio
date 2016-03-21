<?php



namespace SenseioApplication\Model\Entity;


use Phi\Exception;
use Phi\Storage\Memcache;

class URL
{



	protected $url;
	protected $data;
	protected $path;
	protected $depth;
	protected $parentPath;


	protected $parentURL;

	public function __construct($url) {

		$this->url=$url;
		$this->data=parse_url($this->url);
		if(isset($this->data['path'])) {
			$this->path=$this->data['path'];
			$this->parentPath=dirname($this->path);
			$this->depth=count(explode('/', $this->path))-1;
		}
	}


	public function getParentURL() {
		if($this->parentURL===null) {
			if(dirname($this->path)) {
				$this->parentURL=new URL(dirname($this->url));
			}
			else {
				$this->parentURL=false;
			}

		}
		return $this->parentURL;
	}


	public function getDepth() {
		return $this->depth;
	}




	public function __toString() {
		return $this->url;
	}


}
