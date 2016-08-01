<?php
$app->get('/', 'Controllers\HomeController:index')->setName('root');

// Auth Features
$app->get('/register', 'Controllers\RegistrationController:index')->setName('register');
$app->post('/register', 'Controllers\RegistrationController:create')->setName('create_user');

$app->post('/login', 'Controllers\SessionController:create')->setName('create_session');
$app->map(['GET', 'DELETE'], '/logout', 'Controllers\SessionController:destroy')->setName('logout');

// User Features
$app->get('/user', 'Controllers\UserController:index')->setName('user');
$app->post('/user', 'Controllers\UserController:update')->setName('edit_user');

// Room Features
$app->get('/rooms', 'Controllers\RoomController:index')->setName('rooms');
$app->post('/rooms', 'Controllers\RoomController:create')->setName('create_room');
$app->map(['POST', 'DELETE'], '/rooms/{id}/delete', 'Controllers\RoomController:delete')->setName('delete_room');
$app->get('/rooms/search', 'Controllers\RoomController:search')->setName('search_rooms');
$app->post('/rooms/{id}/join', 'Controllers\RoomController:join')->setName('join_room');
$app->post('/rooms/{id}/favourite', 'Controllers\RoomController:favourite')->setName('favourite_room');
$app->get('/rooms/{id}', 'Controllers\RoomController:show')->setName('room');
$app->post('/rooms/{id}/message', 'Controllers\RoomController:message');
$app->get('/rooms/{id}/messages', 'Controllers\RoomController:messages');
$app->get('/rooms/{id}/download', 'Controllers\RoomController:download')->setName('room_save');

// Misc
$app->get('/about', 'Controllers\PageController:about')->setName('about');
$app->get('/help', 'Controllers\PageController:help')->setName('help');
$app->get('/contact', 'Controllers\PageController:contact')->setName('contact');