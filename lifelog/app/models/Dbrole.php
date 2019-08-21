<?php

namespace security;

class Dbrole extends \Phalcon\Mvc\Model
{
    // Setting up Variables
    protected $role;
    protected $description;

    //Setting up functions to access and set variables
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getDescription()
    {
        return $this->description;
    }

    //Initialising relationship
    public function initialize()
    {
        $this->setSchema("lifelog");
        $this->setSource("dbrole");
        $this->hasMany('role', 'security\Dbaccesscontrollist', 'role', ['alias' => 'Dbaccesscontrollist']);
    }

    //Setting up basic functions
    public function getSource()
    {
        return 'dbrole';
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
