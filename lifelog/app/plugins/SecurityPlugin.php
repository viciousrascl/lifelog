<?php

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Adapter\Memory as AclList;
use security\Dbaccesscontrollist;
use security\Dbresource;
use security\Dbaction;
use security\Dbrole;
/**
 * SecurityPlugin
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */
class SecurityPlugin extends Plugin
{
    /**
     * Returns an existing or new access control list
     *
     * @returns AclList
     */
    public function getAcl()
    {
        
        if (!isset($this->persistent->acl)) {

            $acl = new AclList();

            $acl->setDefaultAction(Acl::DENY);
                
            $dbRoles = DBRole::find();
            $dbResources = DBResource::find();
            $dbACLItems = DBaccesscontrollist::find();
            
            // Register roles
            foreach($dbRoles as $dbRole) {
                $acl->addRole($dbRole->getRole());
            }
            
            foreach($dbResources as $dbResource) {
                $dbActions = $dbResource->getDbaction();
                $actions[] = null;
                foreach($dbActions as $dbAction) {
                    array_push($actions,$dbAction->getAction());
                }
                $acl->addResource(new Resource($dbAction->getResource()),$actions);
            }
            
            foreach ($dbACLItems as $ACLItem){
                $acl->allow($ACLItem->getRole(), $ACLItem->getResource(), $ACLItem->getAction());
            }
            
            //The acl is stored in session, APC would be useful here too
            $this->persistent->acl = $acl;
        }

        return $this->persistent->acl;
    }

    /**
     * This action is executed before execute any action in the application
     *
     * @param Event $event
     * @param Dispatcher $dispatcher
     * @return bool
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {

        $auth = $this->session->get('auth');
        if (!$auth){
            $role = 'Guest';
        } else {
            $role = $auth['role'];
        }

        $controller = strtolower($dispatcher->getControllerName());
        $action = strtolower($dispatcher->getActionName());

        $acl = $this->getAcl();
        if (!$acl->isResource($controller)) {
            $dispatcher->forward([
                'controller' => 'errors',
                'action'     => 'show404'
            ]);

            return false;
        }
        
        $allowed = $acl->isAllowed($role, $controller, $action);
        if (!$allowed) {
            $dispatcher->forward(array(
                'controller' => 'errors',
                'action'     => 'show401'
            ));
            return false;
        }
    }
}
?>