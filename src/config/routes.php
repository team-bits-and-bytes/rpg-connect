<?php
$app->get('/', 'Controllers\HomeController:index')->setName('root');

// Auth Features
$app->get('/register', 'Controllers\RegistrationController:index')->setName('register');
$app->post('/register', 'Controllers\RegistrationController:create')->setName('create_user');

$app->get('/login', 'Controllers\SessionController:index')->setName('login');
$app->post('/login', 'Controllers\SessionController:create')->setName('create_session');
$app->map(['GET', 'DELETE'], '/logout', 'Controllers\SessionController:destroy')->setName('logout');

// User Features
$app->get('/user', 'Controllers\UserController:index')->setName('user');
$app->post('/user', 'Controllers\UserController:update')->setName('edit_user');

// Room Features
$app->get('/rooms', 'Controllers\RoomController:index')->setName('rooms');
$app->post('/rooms', 'Controllers\RoomController:create')->setName('create_room');
$app->get('/rooms/search', 'Controllers\RoomController:search')->setName('search_rooms');
$app->post('/rooms/join', 'Controllers\RoomController:join')->setName('join_room');

// Misc
$app->get('/about', 'Controllers\PageController:about')->setName('about');
$app->get('/help', 'Controllers\PageController:help')->setName('help');