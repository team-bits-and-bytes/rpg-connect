<?php
namespace Controllers;

class PageController extends BaseController {
    // GET '/about'
    public function about($request, $response) {
        return $this->renderer->render($response, 'about.twig', $this->locals($request));
    }
    
    // GET '/help'
    public function help($request, $response) {
        return $this->renderer->render($response, 'help.twig', $this->locals($request));
    }
}