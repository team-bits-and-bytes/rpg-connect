<?php

use \Illuminate\Database\Capsule\Manager as Capsule;
use \Illuminate\Events\Dispatcher;
use \Illuminate\Container\Container;

class DatabaseBase {
    protected $schema;
    
    function __construct() {
        $settings = require(__DIR__ . '/../config/settings.php');

        $capsule = new Capsule;
        $capsule->addConnection($settings['settings']['database']);
    
        $capsule->setEventDispatcher(new Dispatcher(new Container));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        
        $this->schema = $capsule->schema();
    }
}