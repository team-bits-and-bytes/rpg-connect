<?php
namespace Controllers;

use Slim\Container;
use Models\User;

class RegistrationController extends BaseController {
    protected $validation_error;
    
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
        $user->username = $params['username'];
        $user->email = $params['email'];
        $user->name = $params['name'];
        $user->password = $params['password'];
        $user->save();
        
        $_SESSION['auth_user'] = $user->id;
        return $user;
    }
    
    // User model validations
    private function validate($params) {
        // unique constraint on username
        $count = User::where('username', $params['username'])->count();
        if ($count != 0) {
            $this->is_invalid = true;
            $this->validation_error ='Username already exists.';
            return false;
        }
        
        // unique constraint on email
        $count = User::where('email', $params['email'])->count();
        if ($count != 0) {
            $this->is_invalid = true;
            $this->validation_error ='Email already exists.';
            return false;
        }
        
        // since we allow sign in via username OR email, don't allow '@' to
        // exist in usernames
        if (strpos($params['username'], '@') !== false) {
            $this->is_invalid = true;
            $this->validation_error ='Username may not contain the \'@\' character.';
            return false;
        }
        
        // minimum length of 6 for the password!
        if (strlen($params['password']) < 6) {
            $this->is_invalid = true;
            $this->validation_error ='Password must be 6 or more characters.';
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