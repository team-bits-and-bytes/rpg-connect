<?php
namespace Controllers;

class PageController extends BaseController {
    // GET '/about'
    public function about($request, $response) {
        return $this->renderer->render($response, 'about.twig', $this->locals($request));
    }
    
    // GET '/gameresources'
    public function gameresources($request, $response) {
        return $this->renderer->render($response, 'gameresources.twig', $this->locals($request));
    }
    
    // GET '/contact'
    public function contact($request, $response) {
        return $this->renderer->render($response, 'contact.twig', $this->locals($request));
    }
    
     // GET '/login'
    public function login($request, $response) {
        return $this->renderer->render($response, 'login.twig', $this->locals($request));
    }
}