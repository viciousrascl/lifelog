<?php

namespace security;

class Dbaction extends \Phalcon\Mvc\Model
{

    // Setting up Variables
    protected $resource;
    protected $action;

    //Setting up functions to access and set variables
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }

    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getAction()
    {
        return $this->action;
    }

    //Initialising relationship
        public function initialize()
    {
        $this->setSchema("lifelog");
        $this->setSource("dbaction");
        $this->hasMany('resource', 'security\Dbaccesscontrollist', 'resource', ['alias' => 'Dbaccesscontrollist']);
        $this->belongsTo('resource', 'security\Dbresource', 'resource', ['alias' => 'Dbresource']);
    }

    //Setting up basic functions
    public function getSource()
    {
        return 'dbaction';
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
