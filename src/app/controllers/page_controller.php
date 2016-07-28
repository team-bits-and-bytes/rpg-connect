<?php
namespace Controllers;

class PageController extends BaseController {
    public function about($request, $response) {
        return $this->renderer->render($response, 'about.twig', $this->locals($request));
    }
}