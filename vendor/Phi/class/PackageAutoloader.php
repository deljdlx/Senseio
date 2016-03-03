<?php
namespace Phi;




class PackageAutoloader
{


	protected $namespaces=array();
	protected $classIndex=false;





	public function addNamespace($namespace, $folder) {
		$this->namespaces[$namespace]=$folder;


        $componentFolders=array('Component', 'Module');



        foreach ($componentFolders as $subFolder) {
            $currentFolder=normalizeFilepath($folder).'/'.$subFolder;

            if(is_dir($currentFolder)) {



                $dir_iterator = new \RecursiveDirectoryIterator($currentFolder);
                $iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::SELF_FIRST);

                foreach ($iterator as $file) {
                    if(strrpos($file, '.php')) {

                        $fileName=str_replace('\\', '/', (string) $file);

                        if(str_replace('.php', '', basename($fileName))==basename(dirname($fileName))) {
                            $fileName=dirname($fileName);
                        }


                        $className=filepathToClassName(str_replace($currentFolder, $namespace.'\\'.$subFolder, $fileName));

                        $this->classIndex[$className]=(string) $file;
                    }
                }
            }

        }
		return $this;
	}


	public function autoload($calledClassName) {

		if(!$this->classIndex) {

			foreach ($this->namespaces as $namespace=>$folder) {




				$folder=normalizeFilepath($folder);


				$dir_iterator = new \RecursiveDirectoryIterator($folder);
				$iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::SELF_FIRST);

				foreach ($iterator as $file) {
					if(strrpos($file, '.php')) {
						$fileName=str_replace('\\', '/', (string) $file);
						$className=filepathToClassName(str_replace($folder, $namespace, $fileName));
						$this->classIndex[$className]=(string) $file;
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