<?php
use lifelog\Blog;
class IndexController extends ControllerBase
{

    public function indexAction()
    {


            $this->dispatcher->forward([
                "controller" => "blog",
                "action" => "search"
            ]);



    }

}

