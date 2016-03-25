<?php

namespace Phi\Resource;




class Manager
{


    protected $resources=array();


    protected $mergedResources=array();


    protected $cssBuffer='';
    protected $javascriptBuffer='';

    protected $deployer;




    public function registerResource($resource) {
        $this->resources[]=$resource;
        return $this;
    }




    public function merge() {
        $this->javascriptBuffer='';
        $this->cssBuffer='';

        foreach ($this->resources as $index=>$resource) {
            if($resource instanceof Javascript) {
                if ($resource->isLocal()) {
                    $this->javascriptBuffer.=file_get_contents($resource->getPath()).';'."\n";
                    $this->mergedResources[$index]=true;
                }
            }
            else if($resource instanceof CSS) {
                if ($resource->isLocal()) {
                    $this->cssBuffer.=file_get_contents($resource->getPath())."\n";
                    $this->mergedResources[$index]=true;
                }
            }
        }
    }




    public function renderHTMLTags($merge=true) {


        if($merge) {
            $this->merge();
        }

        $tags=array();

        foreach ($this->resources as $index=>$resource) {

            if(isset($this->mergedResources[$index])) {
                continue;
            }

            if($resource instanceof Javascript) {
                if($resource->isLocal()) {
                    $url=$this->deployResource($resource);
                }
                else {
                    $url=$resource->getURI();
                }
                $tags[]='<script type="javascript" src="'.$url.'"></script>';
            }

            else if($resource instanceof CSS) {
                if($resource->isLocal()) {
                    $url=$this->deployResource($resource);
                }
                else {
                    $url=$resource->getURI();
                }
                $tags[]='<link rel="stylesheet" href="'.$url.'"></link>';
            }
        }

        $this->deployMergedJavascript();

        return implode("\n", $tags);
    }



    public function setDeployer($callback) {
        $this->deployer=$callback;
        return $this;
    }



    public function deployMergedJavascript() {

        $javascript=new Javascript('string://'.$this->javascriptBuffer);

        return $this->deployResource($javascript);

    }



    public function deployResource($resource) {

        return $this->deployer->deploy($resource);

        /*
        return call_user_func_array(
            $this->deployer,
            array($resource)
        );
        */
    }




}

