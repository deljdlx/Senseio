<?php

namespace Phi\Resource;




Trait HasResources
{

    protected $resourceManager;


    public function setResourceManager($manager) {
        $this->resourceManager=$manager;
        return $this;
    }




    public function getManager() {
        if(!$this->resourceManager) {
            $this->resourceManager=new Manager();
        }
        return $this->resourceManager;
    }


    public function registerResources(Resource $resource) {

        $this->getManager()->addResource($resource);
        return $this;

    }

}

