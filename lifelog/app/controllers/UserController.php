<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use lifelog\User;

class UserController extends ControllerBase
{
	public function indexAction()
    {
        $this->persistent->parameters = null;
    }
	
	public function sendEmailAction($toAddress, $subject, $body)
	{
		$transport = new Swift_SmtpTransport('smtp.gmail.com',587,'tls');
		$transport->setUsername('lifelogclub@gmail.com');
		$transport->setPassword('Snk7c7s12!');
		$transport->setStreamOptions(array('ssl' => array('allow_self_signed' => true, 'verify_peer' => false)));
		$mailer = new Swift_Mailer($transport);
		$message = new Swift_Message($subject);
		$message->setFrom(['lifelogclub@gmail.com' => 'Admin @ lifelog']);
		$message->setTo([$toAddress, $toAddress => $toAddress]);
		$message->setBody($body);
		$result = $mailer->send($message);
		if ($result>0) {
			$this->flash->notice("Email sent sucessfully please Check your Email for further instructions ..... Please Check Spam Folder if email not found");
			$this->dispatcher->forward(["controller" => "user","action" => "changepass"]);
		}
		else {
			$this->flash->notice("Unable send Email to this email address ");
			$this->dispatcher->forward(["controller" => "user","action" => "forgot"]);
		}
		$this->dispatcher->forward(["controller" => "user","action" => "forgot"]);
	}
	
	public function loginAction()
	{
		
	}

    public function logoutAction()
	{
		$this->session->destroy();
		$this->dispatcher->forward(["controller" => "index","action" => "index"]);
		$this->dispatcher->forward([
                "controller" => "blog",
                "action" => "search"
            ]);
	}

    public function authorizeAction()
	{
		$username = $this->request->getPost('username');
		$pass= $this->request->getPost('password');
		$user=User::findFirstByUsername($username);
		if ($user) {
			if ($this->security->checkHash($pass, $user->getpassword())) {
				$this->session->set('auth',
				['username' => $user->getusername(), 
				'role' => $user->getRole(),
				'user_id' => $user->getuserid(),
				]);

			$this->session->set('user',$user);

				$this->flash->success("Welcome back " . $user->getFirstName() . " " . $user->getLastName());
				return $this->dispatcher->forward(["controller" => "blog","action" => "search"]);
			}
			else {
				$this->flash->error("Your password is incorrect - try again");
				return $this->dispatcher->forward(["controller" => "user","action" => "login"]);
			}
		}
		else {
			$this->flash->error("That username was not found - try again");
			return $this->dispatcher->forward(["controller" => "user","action" => "login"]);
		}
		return $this->dispatcher->forward(["controller" => "index","action" => "index"]);
	}
	
	public function forgotAction()
	{
		
	}
	
	public function changepassAction()
	{
		
	}
	
	public function updatepassAction()
	{
		$username = $this->request->getPost('username');
		$pass= $this->request->getPost('dpass');
		$user=User::findFirstByUsername($username);
		if ($user) {
			if ($this->security->checkHash($pass, $user->getpassword())) {
				$user->setPassword($this->security->hash($this->request->getPost("password")));
			if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
				die;
            }
			}
				$this->flash->success("Dear " . $user->getFirstName() . " " . $user->getLastName(). " Your password was Updated Successfully" );
				return $this->dispatcher->forward(["controller" => "user","action" => "login"]);
			}else {
				$this->flash->error("Your password is incorrect - try again");
				return $this->dispatcher->forward(["controller" => "user","action" => "changepass"]);
			}
		}
		else {
			$this->flash->error("That username was not found - try again");
			return $this->dispatcher->forward(["controller" => "user","action" => "changepass"]);
			}
			return $this->dispatcher->forward(["controller" => "index","action" => "index"]);
	}
	
	public function forgotpasswordAction()
	{
		
		$username = $this->request->getPost('username');
		$email= $this->request->getPost('email');
		$pass = uniqid();
		$user=User::findFirstByUsername($username);
		if ($user) {
			$remail = $user->getEmail();
			if ($email == $remail){
			$user->setPassword($this->security->hash($pass));
			if (!$user->save()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }
			}
			$msg = [$remail, "Password Reset Request","Hi ,    Please use a default password  i.e. : " . $pass .  "  to change your password.   Thanks"];
			$this->dispatcher->forward(["conrtoller" => "user","action" => "sendEmail", "params" => $msg]);
			}else {
				$this->flash->error("Your email is not registered - try again");
				return $this->dispatcher->forward(["controller" => "user","action" => "forgot"]);
		}
		}else {
				$this->flash->error("Username not found - try again");
				return $this->dispatcher->forward(["controller" => "user","action" => "forgot"]);
		}
	}

    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, '\lifelog\User', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "user_id";

        $user = User::find($parameters);
        if (count($user) == 0) {
            $this->flash->notice("The search did not find any user");

            $this->dispatcher->forward([
                "controller" => "user",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $user,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    public function newAction()
    {

    }

    public function editAction($user_id)
    {
        if (!$this->request->isPost()) {

            $user = User::findFirstByuser_id($user_id);
            if (!$user) {
                $this->flash->error("user was not found");

                $this->dispatcher->forward([
                    'controller' => "user",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->user_id = $user->getUserId();

            $this->tag->setDefault("user_id", $user->getUserId());
            $this->tag->setDefault("username", $user->getUsername());
            $this->tag->setDefault("password", $user->getPassword());
            $this->tag->setDefault("first_name", $user->getFirstName());
            $this->tag->setDefault("last_name", $user->getLastName());
            $this->tag->setDefault("email", $user->getEmail());
            $this->tag->setDefault("verification", $user->getVerification());
            $this->tag->setDefault("role", $user->getRole());
            $this->tag->setDefault("created_on", $user->getCreatedOn());
            $this->tag->setDefault("profile_pic", $user->getProfilePic());
            
        }
    }

    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "user",
                'action' => 'index'
            ]);

            return;
        }

        $user = new User();
        $user->setUsername(strtolower($this->request->getPost("username")));
        $user->setPassword($this->security->hash($this->request->getPost("password")));
        $user->setFirstName($this->request->getPost("first_name"));
        $user->setLastName($this->request->getPost("last_name"));
        $user->setEmail($this->request->getPost("email"));
        $user->setrole("Registered User");
        $user->setCreatedOn((new DateTime())->format("Y-m-d H:i:s"));
        $user->setProfilePic(base64_encode(file_get_contents($this->request->getUploadedFiles()[0]->getTempName())));
        

        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "user",
                'action' => 'login'
            ]);

            return;
        }

        $this->flash->success("user was created successfully");

        $this->dispatcher->forward([
            'controller' => "user",
            'action' => 'login'
        ]);
    }

    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "user",
                'action' => 'index'
            ]);

            return;
        }

        $user_id = $this->request->getPost("user_id");
        $user = User::findFirstByuser_id($user_id);

        if (!$user) {
            $this->flash->error("user does not exist " . $user_id);

            $this->dispatcher->forward([
                'controller' => "user",
                'action' => 'index'
            ]);

            return;
        }

        $user->setUsername($this->request->getPost("username"));
        $user->setPassword($this->security->hash($this->request->getPost("password")));
        $user->setFirstName($this->request->getPost("first_name"));
        $user->setLastName($this->request->getPost("last_name"));
        $user->setEmail($this->request->getPost("email"));
        $user->setVerification($this->request->getPost("verification"));
        $user->setrole("Registered User");
        $user->setCreatedOn((new DateTime())->format("Y-m-d H:i:s"));
        $user->setProfilePic(base64_encode(file_get_contents($this->request->getUploadedFiles()[0]->getTempName())));
        

        if (!$user->save()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "user",
                'action' => 'edit',
                'params' => [$user->getUserId()]
            ]);

            return;
        }

        $this->flash->success("user was updated successfully");

        $this->dispatcher->forward([
            'controller' => "user",
            'action' => 'index'
        ]);
    }

    public function deleteAction($user_id)
    {
        $user = User::findFirstByuser_id($user_id);
        if (!$user) {
            $this->flash->error("user was not found");

            $this->dispatcher->forward([
                'controller' => "user",
                'action' => 'index'
            ]);

            return;
        }

        if (!$user->delete()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "user",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("user was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "user",
            'action' => "index"
        ]);
    }

}
