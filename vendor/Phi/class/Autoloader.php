<?php
namespace Phi;




class Autoloader
{


	protected $namespaces=array();
	protected $classIndex=false;





	public function addNamespace($namespace, $folder) {
		$this->namespaces[$namespace]=$folder;


        $folder=normalizeFilepath($folder);
        if(is_dir($folder)) {


            $dir_iterator = new \RecursiveDirectoryIterator($folder);

            $iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($iterator as $file) {
                if(strrpos($file, '.php')) {

                    $fileName=str_replace('\\', '/', (string) $file);
                    $className=filepathToClassName(str_replace($folder, $namespace, $fileName));
                    $this->classIndex[strtolower($className)]=(string) $file;
                }
            }
        }



		return $this;
	}


	public function autoload($calledClassName) {


		if(!$this->classIndex) {
			foreach ($this->namespaces as $namespace=>$folder) {

				$folder=normalizeFilepath($folder);

                if(is_dir($folder)) {
                    $dir_iterator = new \RecursiveDirectoryIterator($folder);
                    $iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::SELF_FIRST);

                    foreach ($iterator as $file) {
                        if(strrpos($file, '.php')) {

                            $fileName=str_replace('\\', '/', (string) $file);
                            $className=filepathToClassName(str_replace($folder, $namespace, $fileName));
                            $this->classIndex[strtolower($className)]=(string) $file;
                        }
                    }
                }
			}
		}

		$normalizedClassName=strtolower($calledClassName);


		if(isset($this->classIndex[$normalizedClassName])) {
			include($this->classIndex[$normalizedClassName]);
		}
	}

}