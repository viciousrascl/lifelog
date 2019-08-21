<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use lifelog\Tagblog;

class TagblogController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a tagblog
     *
     * @param string $tag
     */
    public function editAction($tag)
    {
        if (!$this->request->isPost()) {

            $tagblog = Tagblog::findFirstBytag($tag);
            if (!$tagblog) {
                $this->flash->error("tagblog was not found");

                $this->dispatcher->forward([
                    'controller' => "tagblog",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->tag = $tagblog->getTag();

            $this->tag->setDefault("tag", $tagblog->getTag());
            $this->tag->setDefault("blog", $tagblog->getBlog());
            
        }
    }

    /**
     * Creates a new tagblog
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "tagblog",
                'action' => 'index'
            ]);

            return;
        }

        $tagblog = new Tagblog();
        $tagblog->setTag($this->request->getPost("tag"));
        $tagblog->setBlog($this->request->getPost("blog"));
        

        if (!$tagblog->save()) {
            foreach ($tagblog->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "tagblog",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("tagblog was created successfully");

        $this->dispatcher->forward([
            'controller' => "tagblog",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a tagblog edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "tagblog",
                'action' => 'index'
            ]);

            return;
        }

        $tag = $this->request->getPost("tag");
        $tagblog = Tagblog::findFirstBytag($tag);

        if (!$tagblog) {
            $this->flash->error("tagblog does not exist " . $tag);

            $this->dispatcher->forward([
                'controller' => "tagblog",
                'action' => 'index'
            ]);

            return;
        }

        $tagblog->setTag($this->request->getPost("tag"));
        $tagblog->setBlog($this->request->getPost("blog"));
        

        if (!$tagblog->save()) {

            foreach ($tagblog->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "tagblog",
                'action' => 'edit',
                'params' => [$tagblog->getTag()]
            ]);

            return;
        }

        $this->flash->success("tagblog was updated successfully");

        $this->dispatcher->forward([
            'controller' => "tagblog",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a tagblog
     *
     * @param string $tag
     */
    public function deleteAction($tag)
    {
        $tagblog = Tagblog::findFirstBytag($tag);
        if (!$tagblog) {
            $this->flash->error("tagblog was not found");

            $this->dispatcher->forward([
                'controller' => "tagblog",
                'action' => 'index'
            ]);

            return;
        }

        if (!$tagblog->delete()) {

            foreach ($tagblog->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "tagblog",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("tagblog was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "tagblog",
            'action' => "index"
        ]);
    }

}
