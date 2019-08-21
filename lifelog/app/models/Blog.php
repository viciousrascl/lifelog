<?php
namespace lifelog;

class Blog extends \Phalcon\Mvc\Model
{
    
    // Setting up Variables
    protected $blog_id;
    protected $created_by;
    protected $title;
    protected $blog_content;
    protected $blog_created_on;
    protected $blog_image;
    protected $views;

    //Setting up functions to access and set variables
    public function setBlogId($blog_id)
    {
        $this->blog_id = $blog_id;
        return $this;
    }

    public function setCreatedBy($created_by)
    {
        $this->created_by = $created_by;
        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setBlogContent($blog_content)
    {
        $this->blog_content = $blog_content;
        return $this;
    }

    public function setBlogCreatedOn($blog_created_on)
    {
        $this->blog_created_on = $blog_created_on;
        return $this;
    }

    public function setBlogImage($blog_image)
    {
        $this->blog_image = $blog_image;
        return $this;
    }

    public function setViews($views)
    {
        $this->views = $views;
        return $this;
    }

    public function getBlogId()
    {
        return $this->blog_id;
    }

    public function getCreatedBy()
    {
        return $this->created_by;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getBlogContent()
    {
        return $this->blog_content;
    }

    public function getBlogCreatedOn()
    {
        return $this->blog_created_on;
    }

    public function getBlogImage()
    {
        return $this->blog_image;
    }

    public function getViews()
    {
        return $this->views;
    }

    // initialising relationship
    public function initialize()
    {
        $this->setSchema("lifelog");
        $this->setSource("blog");
        $this->hasMany('blog_id', 'lifelog\Comment', 'for_blog', ['alias' => 'Comment']);
        $this->hasMany('blog_id', 'lifelog\Tagblog', 'blog', ['alias' => 'Tagblog']);
        $this->belongsTo('created_by', 'lifelog\User', 'user_id', ['alias' => 'User']);
		$this->hasManyToMany('blog_id','lifelog\tagblog','tag_i','blog_i','lifelog\tag','tag_id',['alias' => 'Tag_i']);
    }

    //Setting up basic functions
    public function getSource()
    {
        return 'blog';
    }

    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function fromInput($dependencyInjector, $modelName, $data)
    {

        $conditions = array();
        if (count($data)) {

            $metaData = $dependencyInjector->getShared('modelsMetadata');

            $model = new $modelName();
            $dataTypes = $metaData->getDataTypes($model);
            $columnMap = $metaData->getReverseColumnMap($model);

            $bind = array();

            foreach ($data as $fieldName => $value) {

                if (isset($columnMap[$fieldName])) {
                    $field = $columnMap[$fieldName];
                } else {
                    continue;
                }

                if (isset($dataTypes[$field])) {

                    if (!is_null($value)) {
                        if ($value != '') {                         
                            if ($dataTypes[$field] == 2) {                              
                                $condition = $fieldName . " LIKE :" . $fieldName . ":";                             
                                $bind[$fieldName] = '%' . $value . '%';
                            } else {                                
                                $condition = $fieldName . ' = :' . $fieldName . ':';
                                $bind[$fieldName] = $value;
                            }
                            $conditions[] = $condition;
                        }
                    }
                }
            }
        }

        $criteria = new Criteria();
        if (count($conditions)) {           
            $criteria->where(join(' AND ', $conditions));
            $criteria->bind($bind);
        }

        return $criteria;

    }
}
