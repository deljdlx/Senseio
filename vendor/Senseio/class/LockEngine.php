<?php



namespace Senseio;
use Phi\Storage\Memcache;


/**
 * Class LockEngine
 * @property Memcache $storage
 * @package Senseio
 *
 */


class LockEngine
{



	const STATUS_LOCKED=1;


	protected $storage;


	public function __construct($storage) {
		$this->storage=$storage;
	}


	public function lock($string) {
		$this->storage->set($this->getHash($string), static::STATUS_LOCKED);
		return $this;
	}

	public function unlockPage($string) {
		$this->storage->delete($this->getHash($string));
		return $this;

	}


	public function isLocked($string) {
		if($this->storage->get($this->getHash($string))) {
			return true;
		}
		else {
			return false;
		}

	}




	public function getHash($string) {
		return md5($string);
	}





}
