<?php
namespace Controllers;

class HomeController extends BaseController {
    public function index($request, $response) {
        $locals = $this->locals($request);
        if ($this->current_user() != null) {
            $locals = array_merge([
                'note' => $this->current_user()->note
            ], $locals);
        }
        return $this->renderer->render($response, 'home.twig', $locals);
    }
}