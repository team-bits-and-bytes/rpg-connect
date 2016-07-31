<?php

require __DIR__ . '/../lib/redis_extensions.php';

use \Illuminate\Database\Capsule\Manager as Capsule;
use \Illuminate\Events\Dispatcher;
use \Illuminate\Container\Container;
use \Slim\Views\Twig;
use \Slim\Views\TwigExtension;

// DIC configuration
$container = $app->getContainer();

// view renderer
$container['renderer'] = function($container) {
    $settings = $container->get('settings')['renderer'];
    
    $view = new Twig($settings['template_path'], [
        'cache' => false    
    ]);
    $view->addExtension(new TwigExtension(
        $container['router'],
        $container['request']->getUri()
    ));

    return $view;
};

// database
$container['database'] = function($container) {
    $capsule = new Capsule;
    $capsule->addConnection($container->get('settings')['database']);
    $capsule->setEventDispatcher(new Dispatcher(new Container));
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

// flash messages
$container['flash'] = function() {
    return new \Slim\Flash\Messages();
};

// Redis
$container['redis'] = function($container) {
    $client = new \Predis\Client([
        'scheme' => 'tcp',
        'host' => $container->get('settings')['redis']['host'],
        'port' => $container->get('settings')['redis']['port']
    ]);
    $profile = $client->getProfile();
    $profile->defineCommand('jsonset', 'RedisSerialization');
    $profile->defineCommand('jsonget', 'RedisDeserialization');
    return $client;
};

/*
This can be enabled in production, to override the default Slim 500 error page.
$container['errorHandler'] = function($container) {
    return function($request, $response, $exception) use ($container) {
        return $container['response']
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write('Something went wrong!');
    };
};
*/