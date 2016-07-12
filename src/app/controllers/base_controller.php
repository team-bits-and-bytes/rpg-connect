<?php
namespace Controllers;

use Slim\Container;
use Models\User;

class BaseController {
    protected $ci;
    protected $renderer;
    protected $database;
    protected $flash;
    
    function __construct(Container $ci) {
        $this->ci = $ci;
        $this->renderer = $ci->get('renderer');
        $this->database = $ci->get('database');
        $this->flash = $ci->get('flash');
    }
    
    public function current_user() {
        if (isset($_SESSION['auth_user'])) {
            return User::find($_SESSION['auth_user']);
        } else {
            return null;
        }
    }
    
    public function locals($request) {
        $name = $request->getAttribute('csrf_name');
        $value = $request->getAttribute('csrf_value');
        $messages = $this->flash->getMessages();
        
        return [
            'csrf_name' => $name,
            'csrf_value' => $value,
            'flashMessages' => $messages,
            'currentUser' => $this->current_user()
        ];
    }
}