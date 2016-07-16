<?php
require __DIR__ . '/../vendor/autoload.php';

// Start PHP session
session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/config/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/config/dependencies.php';

// Register middleware
require __DIR__ . '/../src/config/middleware.php';

// Register models
require __DIR__ . '/../src/app/models/user.php';

// Register controllers
require __DIR__ . '/../src/app/controllers/base_controller.php';
require __DIR__ . '/../src/app/controllers/home_controller.php';
require __DIR__ . '/../src/app/controllers/registration_controller.php';
require __DIR__ . '/../src/app/controllers/session_controller.php';
require __DIR__ . '/../src/app/controllers/user_controller.php';

// Register routes
require __DIR__ . '/../src/config/routes.php';

// Run app
$app->run();