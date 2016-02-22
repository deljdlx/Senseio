<?php



namespace Senseio;




class Link
{

	protected $from;
	protected $to;
	protected $caption;


	public function __construct($from, $to, $caption) {
		$this->from=$from;
		$this->to=$to;
		$this->caption=trim(strip_tags($caption));
		$this->realCaption=$caption;
	}

	public function getCaption() {
		return $this->caption;
	}


	public function from() {
		return $this->from;
	}


	public function to() {
		return $this->to;
	}

	public function isInternal() {
		if(strpos($this->to->getRootURL(), $this->from->getRootURL())===0) {
			return true;
		}
	}


}
