<?php

namespace lifelog;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;

class User extends \Phalcon\Mvc\Model
{

    // Setting up Variables
    protected $user_id;
    protected $username;
    protected $password;
    protected $first_name;
    protected $last_name;
    protected $email;
    protected $verification;
    protected $role;
    protected $created_on;
    protected $profile_pic;

    //Setting up functions to access and set variables
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
        return $this;
    }

    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setVerification($verification)
    {
        $this->verification = $verification;
        return $this;
    }

    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    public function setCreatedOn($created_on)
    {
        $this->created_on = $created_on;
        return $this;
    }

    public function setProfilePic($profile_pic)
    {
        $this->profile_pic = $profile_pic;
        return $this;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getVerification()
    {
        return $this->verification;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getCreatedOn()
    {
        return $this->created_on;
    }

    public function getProfilePic()
    {
        return $this->profile_pic;
    }

    //Setting up email validator
    public function validation()
    {
        $validator = new Validation();
        $validator->add(
            'email',
            new EmailValidator(
                [
                    'model'   => $this,
                    'message' => 'Please enter a correct email address',
                ]
            )
        );
        return $this->validate($validator);
    }

    //Initialising relationship
    public function initialize()
    {
        $this->setSchema("lifelog");
        $this->setSource("user");
        $this->hasMany('user_id', 'lifelog\Blog', 'created_by', ['alias' => 'Blog']);
        $this->hasMany('user_id', 'lifelog\Comment', 'comment_by', ['alias' => 'Comment']);
    }

    
    public function getSource()
    {
        return 'user';
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
