<?php
namespace Controllers;

use Slim\Container;

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
}