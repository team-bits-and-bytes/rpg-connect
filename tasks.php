<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/db/database_base.php';

class Tasks {
    function __construct($args) {
        $this->args = $args;
    }
    
    public function run() {
        if (count($this->args) <= 1) {
            echo "Must pass a task\n";
            return;
        }
        
        switch ($this->args[1]) {
            case 'migrate':
                $this->migrations(false);
                break;
            case 'rollback':
                $this->migrations(true);
                break;
        }
    }
    
    private function migrations($rollback) {
        $files = glob(__DIR__ . '/src/db/migrations/*.php');
        foreach ($files as $file) {
            require_once $file;
            $class = basename($file, '.php');
            // remove the numbers we use for ordering
            $class = str_replace(array(0,1,2,3,4,5,6,7,8,9), "", $class);
            // remove the _'s and capitalize each word
            $class = str_replace(" ", "", ucwords(strtr($class, "_", " ")));
            echo "Migrating ==> " . $class . "\n";
            $obj = new $class;
            if ($rollback == true) {
                $obj->down();
            } else {
                $obj->up();
            }
        }
    }
}

$tasks = new Tasks($argv);
$tasks->run();