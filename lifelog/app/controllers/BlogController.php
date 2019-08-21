<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use lifelog\Blog;
use lifelog\Tagblog;
use lifelog\Tag;

class BlogController extends ControllerBase
{

    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, '\lifelog\Blog', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "blog_id";

        $blog = Blog::find($parameters);
        if (count($blog) == 0) {
            $this->flash->notice("The search did not find any blog");

            $this->dispatcher->forward([
                "controller" => "blog",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $blog,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    public function newAction()
    {

    }

    public function showAction($blog_id)
    {
        $blog = Blog::findFirstByblog_id($blog_id);
            if (!$blog) {
                $this->flash->error("blog was not found");

                $this->dispatcher->forward([
                    'controller' => "blog",
                    'action' => 'index'
                ]);

                return;
            }
        $this->view->blog_id = $blog->getBlogId();
        
    }

    public function editAction($blog_id)
    {
        if (!$this->request->isPost()) {

            $blog = Blog::findFirstByblog_id($blog_id);
            if (!$blog) {
                $this->flash->error("blog was not found");

                $this->dispatcher->forward([
                    'controller' => "blog",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->blog_id = $blog->getBlogId();

            $this->tag->setDefault("blog_id", $blog->getBlogId());
            $this->tag->setDefault("created_by", $blog->getCreatedBy());
            $this->tag->setDefault("title", $blog->getTitle());
            $this->tag->setDefault("blog_content", $blog->getBlogContent());
            $this->tag->setDefault("blog_created_on", $blog->getBlogCreatedOn());
            $this->tag->setDefault("blog_image", $blog->getBlogImage());
            $this->tag->setDefault("views", $blog->getViews());
            
        }
    }


    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "blog",
                'action' => 'index'
            ]);

            return;
        }

        $blog = new Blog();
        $auth = $this->session->get('auth');
        $blog->setCreatedBy($auth['user_id']);
        $blog->setTitle($this->request->getPost("title"));
        $blog->setBlogContent($this->request->getPost("blog_content"));
        $blog->setBlogCreatedOn((new DateTime())->format("Y-m-d H:i:s"));
        $blog->setBlogImage(base64_encode(file_get_contents($this->request->getUploadedFiles()[0]->getTempName())));
       
        if (!$blog->save()) {
            foreach ($blog->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "blog",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("blog was created successfully");

        $this->dispatcher->forward([
            'controller' => "blog",
            'action' => 'search'
        ]);
    }

    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "blog",
                'action' => 'index'
            ]);

            return;
        }

        $blog_id = $this->request->getPost("blog_id");
        $blog = Blog::findFirstByblog_id($blog_id);

        if (!$blog) {
            $this->flash->error("blog does not exist " . $blog_id);

            $this->dispatcher->forward([
                'controller' => "blog",
                'action' => 'index'
            ]);

            return;
        }

        $auth = $this->session->get('auth');
        $blog->setCreatedBy($auth['user_id']);
        $blog->setTitle($this->request->getPost("title"));
        $blog->setBlogContent($this->request->getPost("blog_content"));
        $blog->setBlogImage(base64_encode(file_get_contents($this->request->getUploadedFiles()[0]->getTempName())));
        
        

        if (!$blog->save()) {

            foreach ($blog->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "blog",
                'action' => 'edit',
                'params' => [$blog->getBlogId()]
            ]);

            return;
        }

        $this->flash->success("blog was updated successfully");

        $this->dispatcher->forward([
            'controller' => "blog",
            'action' => 'index'
        ]);
    }

    public function deleteAction($blog_id)
    {
        $blog = Blog::findFirstByblog_id($blog_id);
        if (!$blog) {
            $this->flash->error("blog was not found");

            $this->dispatcher->forward([
                'controller' => "blog",
                'action' => 'index'
            ]);

            return;
        }

        if (!$blog->delete()) {

            foreach ($blog->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "blog",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("blog was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "blog",
            'action' => "index"
        ]);
    }

}
