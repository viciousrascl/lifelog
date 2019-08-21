<?php

namespace lifelog;

class Comment extends \Phalcon\Mvc\Model
{

    // Setting up Variables
    protected $comment_id;
    protected $comment_by;
    protected $for_blog;
    protected $comment_data;
    protected $commented_on;

    //Setting up functions to access and set variables
    public function setCommentId($comment_id)
    {
        $this->comment_id = $comment_id;
        return $this;
    }

    public function setCommentBy($comment_by)
    {
        $this->comment_by = $comment_by;
        return $this;
    }

    public function setForBlog($for_blog)
    {
        $this->for_blog = $for_blog;
        return $this;
    }

    public function setCommentData($comment_data)
    {
        $this->comment_data = $comment_data;
        return $this;
    }

    public function setCommentedOn($commented_on)
    {
        $this->commented_on = $commented_on;
        return $this;
    }

    public function getCommentId()
    {
        return $this->comment_id;
    }

    public function getCommentBy()
    {
        return $this->comment_by;
    }

    public function getForBlog()
    {
        return $this->for_blog;
    }

    public function getCommentData()
    {
        return $this->comment_data;
    }

    public function getCommentedOn()
    {
        return $this->commented_on;
    }

    // initialising relationship
    public function initialize()
    {
        $this->setSchema("lifelog");
        $this->setSource("comment");
        $this->belongsTo('comment_by', 'lifelog\User', 'user_id', ['alias' => 'User']);
        $this->belongsTo('for_blog', 'lifelog\Blog', 'blog_id', ['alias' => 'Blog']);
    }

    //Setting up basic functions
    public function getSource()
    {
        return 'comment';
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
