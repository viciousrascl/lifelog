<?php

namespace security;

class Dbresource extends \Phalcon\Mvc\Model
{

    // Setting up Variables
    protected $resource;

    //Setting up functions to access and set variables
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }

    public function getResource()
    {
        return $this->resource;
    }

    //Initialising relationship
    public function initialize()
    {
        $this->setSchema("lifelog");
        $this->setSource("dbresource");
        $this->hasMany('resource', 'security\Dbaction', 'resource', ['alias' => 'Dbaction']);
    }

    //Setting up basic functions
    public function getSource()
    {
        return 'dbresource';
    }

    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
