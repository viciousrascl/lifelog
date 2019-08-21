<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use security\Dbaccesscontrollist;
use security\Dbresource;
use security\Dbaction;
use security\Dbrole;

class DbaccesscontrollistController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }
	public function setAccessControlAction($resource)
	{
		$this->populateAclAction($resource);
		$this->view->resource=$resource;
		$this->view->roles = Dbrole::find();
		$this->view->actions = Dbaction::findByResource($resource);
		$this->view->aclItems = Dbaccesscontrollist::findByResource($resource);
	}
	public function saveAccessControlAction()
{
    $resource = $this->request->getPost('resource');
    
    //delete all pre-existing access control settings for this resource
    $dbACLCurrentItems = Dbaccesscontrollist::findByResource($resource);
    foreach($dbACLCurrentItems as $dbACLCurrentItem) {
        $dbACLCurrentItem->delete();
    }
    $aclItemsArray = $this->request->getPost('aclItem');
    $msg = "No updates to the Acl";
    if(!empty($aclItemsArray)) {
        foreach($aclItemsArray as $role => $actionsArray) {
            foreach($actionsArray as $action => $y) {
                $aclItem=new Dbaccesscontrollist();
                $aclItem->setRole($role);
                $aclItem->setResource($resource);
                $aclItem->setAction($action);
                $aclItem->save();
                $msg="The Acl has been updated";
            }
        }
    }

    $this->flash->notice($msg);
    $this->dispatcher->forward([
        "controller" => "index",
        "action" => "index"
    ]);        
    
}

    /**
     * Searches for dbaccesscontrollist
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, '\security\Dbaccesscontrollist', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "role";

        $dbaccesscontrollist = Dbaccesscontrollist::find($parameters);
        if (count($dbaccesscontrollist) == 0) {
            $this->flash->notice("The search did not find any dbaccesscontrollist");

            $this->dispatcher->forward([
                "controller" => "dbaccesscontrollist",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $dbaccesscontrollist,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a dbaccesscontrollist
     *
     * @param string $role
     */
    public function editAction($role)
    {
        if (!$this->request->isPost()) {

            $dbaccesscontrollist = Dbaccesscontrollist::findFirstByrole($role);
            if (!$dbaccesscontrollist) {
                $this->flash->error("dbaccesscontrollist was not found");

                $this->dispatcher->forward([
                    'controller' => "dbaccesscontrollist",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->role = $dbaccesscontrollist->getRole();

            $this->tag->setDefault("role", $dbaccesscontrollist->getRole());
            $this->tag->setDefault("action", $dbaccesscontrollist->getAction());
            $this->tag->setDefault("resource", $dbaccesscontrollist->getResource());
            
        }
    }

    /**
     * Creates a new dbaccesscontrollist
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "dbaccesscontrollist",
                'action' => 'index'
            ]);

            return;
        }

        $dbaccesscontrollist = new Dbaccesscontrollist();
        $dbaccesscontrollist->setRole($this->request->getPost("role"));
        $dbaccesscontrollist->setAction($this->request->getPost("action"));
        $dbaccesscontrollist->setResource($this->request->getPost("resource"));
        

        if (!$dbaccesscontrollist->save()) {
            foreach ($dbaccesscontrollist->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "dbaccesscontrollist",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("dbaccesscontrollist was created successfully");

        $this->dispatcher->forward([
            'controller' => "dbaccesscontrollist",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a dbaccesscontrollist edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "dbaccesscontrollist",
                'action' => 'index'
            ]);

            return;
        }

        $role = $this->request->getPost("role");
        $dbaccesscontrollist = Dbaccesscontrollist::findFirstByrole($role);

        if (!$dbaccesscontrollist) {
            $this->flash->error("dbaccesscontrollist does not exist " . $role);

            $this->dispatcher->forward([
                'controller' => "dbaccesscontrollist",
                'action' => 'index'
            ]);

            return;
        }

        $dbaccesscontrollist->setRole($this->request->getPost("role"));
        $dbaccesscontrollist->setAction($this->request->getPost("action"));
        $dbaccesscontrollist->setResource($this->request->getPost("resource"));
        

        if (!$dbaccesscontrollist->save()) {

            foreach ($dbaccesscontrollist->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "dbaccesscontrollist",
                'action' => 'edit',
                'params' => [$dbaccesscontrollist->getRole()]
            ]);

            return;
        }

        $this->flash->success("dbaccesscontrollist was updated successfully");

        $this->dispatcher->forward([
            'controller' => "dbaccesscontrollist",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a dbaccesscontrollist
     *
     * @param string $role
     */
    public function deleteAction($role)
    {
        $dbaccesscontrollist = Dbaccesscontrollist::findFirstByrole($role);
        if (!$dbaccesscontrollist) {
            $this->flash->error("dbaccesscontrollist was not found");

            $this->dispatcher->forward([
                'controller' => "dbaccesscontrollist",
                'action' => 'index'
            ]);

            return;
        }

        if (!$dbaccesscontrollist->delete()) {

            foreach ($dbaccesscontrollist->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "dbaccesscontrollist",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("dbaccesscontrollist was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "dbaccesscontrollist",
            'action' => "index"
        ]);
    }
		public function populateAclAction($resource)
	{
		$dir = "../app/controllers/";
		$className = (ucfirst($resource . "Controller"));
		$controllerFile = $dir . $className . ".php";
		//tyring to include a file with the same name as the current script causes a conflict
		if(strcmp($resource,"dbaccesscontrollist")!=0) {
			if((@include $controllerFile) === false) {
				$this->flash->error("No such resource");
				return;
			}
		}
		$thisClass = new $className();
		$funcs = get_class_methods($thisClass);
		unset($thisClass);
		$resc = new Dbresource();
		$resc->setResource($resource);
		if (!$resc->save()) {
			foreach ($resc->getMessages() as $message) {
				$this->flash->error($message);
			}
			return;
		}
		//create an action in the database for each of the functions of the controller                    
		foreach($funcs as $func) {
			if(strpos($func,"Action")){
				$action = new Dbaction();
				$action->setResource($resource);
				$action->setAction(substr($func,0,-6));
				if (!$action->save()) {
					foreach ($action->getMessages() as $message) {
						$this->flash->error($message);
					}
					return;
				}
			}
		}
		
	}
}
