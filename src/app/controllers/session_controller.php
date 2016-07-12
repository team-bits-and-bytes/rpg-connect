<?php

namespace Controllers;

use Models\User;

class SessionController extends BaseController {
    // GET '/login'
    public function index($request, $response) {
        // redirect if the user is already authenticated
        if ($this->current_user() != null) {
            $this->flash->addMessage('info', 'You are already logged in.');
            return $response->withRedirect($this->ci->get('router')->pathFor('root'));
        }
        return $this->renderer->render($response, 'login.twig', $this->locals($request));
    }
    
    // POST '/login'
    public function create($request, $response) {
        $user = User::where('email', $request->getParam('email'))->first();
        if (is_null($user)) {
            $this->flash->addMessage('error', 'The credentials you passed were invalid.');
            return $response->withRedirect($this->ci->get('router')->pathFor('login'));
        }
        
        // verify that the password hashes match
        if (password_verify($request->getParam('password'), $user->password)) {
            $_SESSION['auth_user'] = $user->id;
            $this->flash->addMessage('success', 'You are now logged in.');
            return $response->withRedirect($this->ci->get('router')->pathFor('root'));
        } else {
            $this->flash->addMessage('error', 'The credentials you passed were invalid.');
            return $response->withRedirect($this->ci->get('router')->pathFor('login'));
        }
    }
    
    // GET '/logout'
    // DELETE '/logout'
    public function destroy($request, $response) {
        unset($_SESSION['auth_user']);
        $this->flash->addMessage('success', 'You are now logged out.');
        return $response->withRedirect($this->ci->get('router')->pathFor('root'));
    }
}