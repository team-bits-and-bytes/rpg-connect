<?php
namespace Controllers;

class UserController extends BaseController {
    public function index($request, $response) {
        // redirect if the user isn't authenticated
        if ($this->current_user() == null) {
            $this->flash->addMessage('info', 'You must login first.');
            return $response->withRedirect($this->ci->get('router')->pathFor('root'));
        }
        
        return $this->renderer->render($response, 'user.twig', $this->locals($request));
    }
    
    public function update($request, $response) {
        $params = $request->getParams();
        
        // check if email exists for another account if it has changed
        if ($params['email'] != $this->current_user()->email) {
            $count = User::where('email', $params['email'])->count();
            if ($count != 0) {
                $this->flash->addMessage('error', 'An account with this email address already exists.');
                return $response->withRedirect($this->ci->get('router')->pathFor('user'));
            }
        }
        
        // update the attributes
        $user = $this->current_user();
        if (password_verify($params['password'], $user->password)) {
            $user->email = $params['email'];
            $user->name = $params['name'];
            $user->avatar = $params['avatar'];
            $user->location = $params['location'];
            $user->website = $params['website'];
            $user->about = $params['about'];
            
            // new password set?
            if ($params['new_password'] != null) {
                if ($params['new_password'] == $params['new_password_confirm']) {
                    $user->password = $params['new_password'];
                } else {
                    $this->flash->addMessage('error', 'New passwords do not match.');
                    return $response->withRedirect($this->ci->get('router')->pathFor('user'));
                }
            }
            $user->save();
        } else {
            $this->flash->addMessage('error', 'You must enter your current password.');
            return $response->withRedirect($this->ci->get('router')->pathFor('user'));
        }
        
        $this->flash->addMessage('success', 'Your account has been updated!');
        return $response->withRedirect($this->ci->get('router')->pathFor('root'));
    }
}