<?php

namespace security;

class Errors extends \Phalcon\Mvc\Model
{//Initialising relationship
    public function initialize()
    {
        $this->setSchema("lifelog");
        $this->setSource("errors");
    }

    //Setting up basic functions
    public function getSource()
    {
        return 'errors';
    }
   }