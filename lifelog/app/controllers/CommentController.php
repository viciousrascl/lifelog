<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use lifelog\Comment;

class CommentController extends ControllerBase
{

    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, '\lifelog\Comment', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "comment_id";

        $comment = Comment::find($parameters);
        if (count($comment) == 0) {
            $this->flash->notice("The search did not find any comment");

            $this->dispatcher->forward([
                "controller" => "comment",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $comment,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    public function newAction()
    {

    }

    public function editAction($comment_id)
    {
        if (!$this->request->isPost()) {

            $comment = Comment::findFirstBycomment_id($comment_id);
            if (!$comment) {
                $this->flash->error("comment was not found");

                $this->dispatcher->forward([
                    'controller' => "comment",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->comment_id = $comment->getCommentId();

            $this->tag->setDefault("comment_id", $comment->getCommentId());
            $this->tag->setDefault("comment_by", $comment->getCommentBy());
            $this->tag->setDefault("for_blog", $comment->getForBlog());
            $this->tag->setDefault("comment_data", $comment->getCommentData());
            $this->tag->setDefault("commented_on", $comment->getCommentedOn());
            
        }
    }

    public function createAction($for_blog)
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "comment",
                'action' => 'index'
            ]);

            return;
        }

        $comment = new Comment();
        $auth = $this->session->get('auth');
        $comment->setCommentBy($auth['user_id']);
        $comment->setForBlog($for_blog);
        $comment->setCommentData($this->request->getPost("comment_data"));
        $comment->setCommentedOn((new DateTime())->format("Y-m-d H:i:s"));
        

        if (!$comment->save()) {
            foreach ($comment->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "comment",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("comment was created successfully");

        $this->dispatcher->forward([
            'controller' => "blog",
            'action' => 'show' 
        ]);
    }

    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "comment",
                'action' => 'index'
            ]);

            return;
        }

        $comment_id = $this->request->getPost("comment_id");
        $comment = Comment::findFirstBycomment_id($comment_id);

        if (!$comment) {
            $this->flash->error("comment does not exist " . $comment_id);

            $this->dispatcher->forward([
                'controller' => "comment",
                'action' => 'index'
            ]);

            return;
        }

        $comment->setCommentBy($this->request->getPost("comment_by"));
        $comment->setForBlog($this->request->getPost("for_blog"));
        $comment->setCommentData($this->request->getPost("comment_data"));
        $comment->setCommentedOn((new DateTime())->format("Y-m-d H:i:s"));
        

        if (!$comment->save()) {

            foreach ($comment->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "comment",
                'action' => 'edit',
                'params' => [$comment->getCommentId()]
            ]);

            return;
        }

        $this->flash->success("comment was updated successfully");

        $this->dispatcher->forward([
            'controller' => "comment",
            'action' => 'index'
        ]);
    }

    public function deleteAction($comment_id)
    {
        $comment = Comment::findFirstBycomment_id($comment_id);
        if (!$comment) {
            $this->flash->error("comment was not found");

            $this->dispatcher->forward([
                'controller' => "comment",
                'action' => 'index'
            ]);

            return;
        }

        if (!$comment->delete()) {

            foreach ($comment->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "comment",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("comment was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "comment",
            'action' => "index"
        ]);
    }

}
