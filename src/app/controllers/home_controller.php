<?php
namespace Controllers;

class HomeController extends BaseController {
    public function index($request, $response) {
        return $this->renderer->render($response, 'index.twig', $this->locals($request));
    }
}