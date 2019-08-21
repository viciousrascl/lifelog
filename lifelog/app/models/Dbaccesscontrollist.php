<?php

namespace security;

class Dbaccesscontrollist extends \Phalcon\Mvc\Model
{

    // Setting up Variables
    protected $role;
    protected $action;
    protected $resource;

    //Setting up functions to access and set variables
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getResource()
    {
        return $this->resource;
    }

    //Initialising relationship
    public function initialize()
    {
        $this->setSchema("lifelog");
        $this->setSource("dbaccesscontrollist");
        $this->belongsTo('role', 'security\Dbrole', 'role', ['alias' => 'Dbrole']);
        $this->belongsTo('resource', 'security\Dbaction', 'resource', ['alias' => 'Dbaction']);
    }

    //Setting up basic functions
    public function getSource()
    {
        return 'dbaccesscontrollist';
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
