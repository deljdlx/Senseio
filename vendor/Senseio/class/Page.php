<?php



namespace Senseio;




class Page
{




	protected $host;
	protected $sheme;
	protected $url;

	protected $buffer;
	protected $rootURL;

	protected $links=null;
	protected $toPages;


	protected $headers;

	protected $bufferSize;
	protected $weight;

	protected $loadingTime;

	protected $title;
	protected $canonical;


	protected $exists;

	protected $statusCode;

	public function __construct($url) {

		$this->url=preg_replace('`/$`', '', $url);
		$data=parse_url($url);


        if(isset($data['scheme']) && isset($data['host'])) {
            $this->rootURL=$data['scheme'].'://'.$data['host'];
        }


	}






	public function getRootURL() {
		return $this->rootURL;
	}



	public function getHeaders() {
		if($this->headers===null) {
			stream_context_set_default(
				array(
					'http' => array(
						'method' => 'HEAD',
						'header' => array(
							'Connection: close', // No Keep-Alive
							'Accept-Encoding: gzip, deflate' // We support content compression
						)
					)
				)
			);
			$this->headers=get_headers($this->url, true);

			$this->statusCode=$this->headers[0];



			/*
			if(strpos($this->headers[0], '200 OK')) {
				$this->exists=true;
			}
			*/


			if(isset($this->headers['Content-Length'])) {
				$this->weight=$this->headers['Content-Length'];
			}

		}
		return $this->headers;
	}

	public function exists() {


		if($this->exists===null) {
			$this->getHeaders();
		}
		return $this->exists;
	}

	public function getStatusCode() {

		if($this->statusCode===null) {
			$this->getHeaders();
		}
		return $this->statusCode;
	}




	public function get() {

		/*
		if(!$this->exists()) {
			return false;
		}
		*/


		if($this->buffer===null) {
			stream_context_set_default(
				array(
					'http' => array(
						'method' => 'GET',
						'header' => array(
							'Connection: close', // No Keep-Alive
						)
					)
				)
			);

			$start=microtime(true);
			$this->buffer=file_get_contents($this->url);
			$this->loadingTime=microtime(true)-$start;
			$this->bufferSize=strlen($this->buffer);
		}
		return $this->buffer;
	}


	public function getData() {
		return array(
			'url'=>$this->getURL(),
			'status'=>$this->getStatusCode(),
			'canonical'=>$this->getCanonical(),
			'loadingTime'=>$this->getLoadingTime(),
			'weight'=>$this->getWeight(),
			'bufferSize'=>$this->getBufferSize(),
			'title'=>$this->getTitle(),
			'content'=>$this->buffer

		);
	}





	public function getLoadingTime() {
		if($this->loadingTime===null) {
			$this->loadingTime=0;
			$this->get();
		}
		return $this->loadingTime;
	}

	public function getWeight() {
		if($this->weight===null) {
			$this->weight=false;
			$this->getHeaders();
		}
		return $this->weight;
	}

	public function getBufferSize() {
		if($this->bufferSize===null) {
			$this->bufferSize=false;
			$this->get();
		}
		return $this->bufferSize;
	}



	public function getLinks() {
		if($this->links===null) {
			$this->links=array();
			$this->links=$this->extractLinks($this->get());
		}
		return $this->links;
	}

	public function getURL() {
		return $this->url;
	}



	public function getTitle() {
		if($this->title===null) {
			$this->title=false;
			$buffer=$this->get();
			preg_match('`<title.*?>(.*?)</title.*?>`si', $buffer, $matches);
			if(isset($matches[1])) {
				$this->title=html_entity_decode($matches[1]);
			}
		}
		return $this->title;
	}

	public function getCanonical() {
		if($this->canonical===null) {
			$this->canonical=false;
			$buffer=$this->get();
			preg_match('`<link\s+[^><]*?rel=("|\')canonical\1\s+[^><]*?href=("|\')([^><]+?)\2[^><]*?>`si', $buffer, $matches);

			if(isset($matches[3])) {
				$this->canonical=$matches[3];
			}
		}
		return $this->canonical;
	}




	public function extractLinks($buffer) {


		preg_match_all('`<a .*?href\s*=\s*(\'|")(.*?)\1.*?>(.*?)</a>`', $buffer, $matches);

		$links=$matches[2];
		$captions=$matches[3];



		foreach ($links as &$url) {
			if(preg_match('`^/`', $url)) {
				$url=$this->rootURL.$url;
			}
		}


		$filteredLinks=array();



		foreach ($links as $index=>$url) {
			if($url) {
				if(!isset($links[$url])) {
					if (!(strpos($url, 'javascript:') === 0)) {
						if (!(strpos($url, '#') === 0)) {
							$links[$url]=new Page($url);
							$link = new Link($this, $links[$url], $captions[$index]);
							$filteredLinks[] = $link;
						}
					}
				}
			}
		}


		return $filteredLinks;

	}
}





