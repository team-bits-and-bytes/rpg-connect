<?php
$app->get('/', 'Controllers\HomeController:index')->setName('root');

// User Features
$app->get('/login', 'Controllers\SessionController:index')->setName('login');
$app->post('/login', 'Controllers\SessionController:create')->setName('create_session');
$app->map(['GET', 'DELETE'], '/logout', 'Controllers\SessionController:destroy')->setName('logout');

$app->get('/register', 'Controllers\UsersController:index')->setName('register');
$app->post('/users', 'Controllers\UsersController:create')->setName('create_user');