<?php
namespace Controllers;

class HomeController extends BaseController {
    public function index($request, $response) {
        return $this->renderer->render($response, 'home.twig', $this->locals($request));
    }
}