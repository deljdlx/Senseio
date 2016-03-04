<?php



namespace Senseio;


class Component
{







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
