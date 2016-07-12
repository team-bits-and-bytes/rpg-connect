<?php
namespace Controllers;

use Slim\Container;
use Models\User;

class UsersController extends BaseController {
    protected $validation_errors;
    
    function __construct(Container $ci) {
        parent::__construct($ci);
        $this->is_invalid = false;
        $this->validation_error = '';
    }
    
    // GET '/register'
    public function index($request, $response) {
        // redirect if the user is already authenticated
        if ($this->current_user() != null) {
            $this->flash->addMessage('info', 'You are already logged in.');
            return $response->withRedirect($this->ci->get('router')->pathFor('root'));
        }
        
        $name = $request->getAttribute('csrf_name');
        $value = $request->getAttribute('csrf_value');
        $messages = $this->flash->getMessages();
        
        return $this->renderer->render($response, 'register.twig', $this->locals($request));
    }
    
    // POST '/users'
    public function create($request, $response) {
        $user = $this->createUser($request->getParams());
        if (is_null($user)) {
            if ($this->is_invalid == true) {
                $this->flash->addMessage('error', $this->validation_error);
            }
            return $response->withRedirect($this->ci->get('router')->pathFor('register'));
        }
        $this->flash->addMessage('success', 'Your account was successfully created.');
        return $response->withRedirect($this->ci->get('router')->pathFor('root'));
    }
    
    // Creates a User record, if we pass our validations
    private function createUser($params) {
        if (!$this->validate($params)) {
            return null;
        }
        
        $user = new User;
        $user->email = $params['email'];
        $user->name = $params['name'];
        $user->password = $params['password'];
        $user->save();
        
        $_SESSION['auth_user'] = $user->id;
        return $user;
    }
    
    // User model validations
    private function validate($params) {
        // unique constraint on email
        $count = User::where('email', $params['email'])->count();
        if ($count != 0) {
            $this->is_invalid = true;
            $this->validation_error ='Email already exists.';
            return false;
        }
        
        // passwords don't match
        if ($params['password'] != $params['password_confirm']) {
            $this->is_invalid = true;
            $this->validation_error = 'Passwords do not match.';
            return false;
        }
        
        return true;
    }
}