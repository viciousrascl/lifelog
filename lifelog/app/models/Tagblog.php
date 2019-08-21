<?php

namespace lifelog;

class Tagblog extends \Phalcon\Mvc\Model
{

    // Setting up Variables
    protected $tag_i;
    protected $blog_i;

    //Setting up functions to access and set variables
    public function setTagi($tag_i)
    {
        $this->tag_i = $tag_i;
        return $this;
    }

    public function setBlogi($blog_i)
    {
        $this->blog_i = $blog_i;
        return $this;
    }

    public function getTagi()
    {
        return $this->tag_i;
    }

    public function getBlogi()
    {
        return $this->blog_i;
    }

    //Initialising relationship
    public function initialize()
    {
        $this->setSchema("lifelog");
        $this->setSource("tagblog");
        $this->belongsTo('blog_i', 'lifelog\Blog', 'blog_id', ['alias' => 'Blogi']);
        $this->belongsTo('tag_i', 'lifelog\Tag', 'Tag_id', ['alias' => 'Tagi']);
    }

    //Setting up basic functions
    public function getSource()
    {
        return 'tagblog';
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
