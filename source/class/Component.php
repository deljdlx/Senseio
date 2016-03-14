<?php


namespace SenseioApplication;



use SenseioApplication\Model\Repository;

class Component extends \Senseio\Component
{




	protected $repository;


	public function __construct($repository=null) {
		if(!$repository) {
			$this->repository=new Repository();
		}
		else {
			$this->repository=$repository;
		}

	}



	public function render() {

		$reflector=new \ReflectionClass($this);
		$filepath=dirname($reflector->getFileName());


		$assetFilepath=$filepath.'/asset';

		$buffer='';

		if(is_dir($assetFilepath)) {
			$handler=opendir($assetFilepath);


			while ($fileName=readdir($handler)) {
				if($fileName!='.' && $fileName!='..') {

					$data=pathinfo($assetFilepath.'/'.$fileName);


					if($data['extension']=='js') {
						$buffer.="<script>\n".file_get_contents($assetFilepath.'/'.$fileName)."\n</script>";
					}
					elseif($data['extension']=='css') {
						$buffer.="<style>\n".file_get_contents($assetFilepath.'/'.$fileName)."\n<style>";
					}
				}
			}
			closedir($handler);
		}

		return $buffer;



	}
}
