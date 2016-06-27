<?php
namespace Controllers;

class HomeController extends BaseController {
    public function index($request, $response) {
        $messages = $this->flash->getMessages();
        return $this->renderer->render($response, 'index.html', [
            'flashMessages' => $messages
        ]);
    }
}