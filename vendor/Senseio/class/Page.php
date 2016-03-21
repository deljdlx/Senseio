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


	protected $depth=0;



	protected $crawlStatus=0;





	const STATUS_LOCKED='LOCKED';
	const STATUS_CREATED=0;
	const STATUS_CRAWLING=1;
	const STATUS_CRAWLED=2;



	public function __construct($url, $depth=0) {

		$this->depth=$depth;

		$this->url=preg_replace('`/$`', '', $url);
		$data=parse_url($url);


        if(isset($data['scheme']) && isset($data['host'])) {
            $this->rootURL=$data['scheme'].'://'.$data['host'];
        }
	}


	public function getCrawlStatus() {
		return $this->crawlStatus;
	}


	public function setCrawlStatus($status) {
		$this->crawlStatus=$status;
		return $this;
	}





	public function setContent($buffer) {
		$this->buffer=$buffer;
		return $this;
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
							'Accept-Encoding: gzip, deflate', // We support content compression
							'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:44.0) Gecko/20100101 Firefox/44.0'

						)
					)
				)
			);
			$headers=get_headers($this->url, true);

			foreach ($headers as $key => &$value) {
				if(is_array($value)) {
					$value=end($value);
				}
			}

			foreach ($headers as $key => &$value) {
				if((int) $value) {
					$value=(int) $value;
				}
			}


			$this->headers=$headers;


			$this->statusCode=$this->headers[0];




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


		$headers=$this->getHeaders();

		$buffer=null;

		if(isset($headers['Content-Type'])) {

			if(is_array($headers['Content-Type'])) {
				print_r($headers);
				die('EXIT '.__FILE__.'@'.__LINE__);
			}


			if(strpos($headers['Content-Type'], 'text/')) {
				$buffer=$this->buffer;
			}
		}



		if(!$this->getStatusCode()) {
			print_r($this->getHeaders());
			die('EXIT '.__FILE__.'@'.__LINE__);
		}





		return array(
			'url'=>$this->getURL(),
			'status'=>$this->getStatusCode(),
			'canonical'=>$this->getCanonical(),
			'loadingTime'=>$this->getLoadingTime(),
			'weight'=>$this->getWeight(),
			'bufferSize'=>$this->getBufferSize(),
			'title'=>$this->getTitle(),
			'normalizedTitle'=>$this->getTitle(),
			'content'=>$buffer,
			'depth'=>$this->depth,
            'headers'=>$headers,
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


		preg_match_all('`<a\s+[^>]*?href\s*=\s*(\'|")([^>]*?)\1[^>]*?>(.*?)</a>`si', $buffer, $matches);

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
							$links[$url]=new Page($url, $this->depth+1);
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





