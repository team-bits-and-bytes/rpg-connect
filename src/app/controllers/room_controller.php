<?php
namespace Controllers;

use Slim\Container;
use Models\Room;
use Models\Member;

class RoomController extends BaseController {
    protected $validation_error;
    
    function __construct(Container $ci) {
        parent::__construct($ci);
        $this->is_invalid = false;
        $this->validation_error = '';
    }
    
    // GET '/rooms'
    public function index($request, $response) {
        // redirect if the user isn't authenticated
        if ($this->current_user() == null) {
            return $response->withRedirect($this->ci->get('router')->pathFor('login'));
        }
        return $this->renderer->render($response, 'rooms.twig', $this->locals($request));
    }
    
    // POST '/rooms'
    public function create($request, $response) {
        $room = $this->createRoom($request->getParams());
        if (is_null($room)) {
            if ($this->is_invalid == true) {
                $this->flash->addMessage('error', $this->validation_error);
            }
            return $response->withRedirect($this->ci->get('router')->pathFor('rooms'));
        }
        $this->flash->addMessage('success', 'Your room was successfully created.');
        return $response->withRedirect($this->ci->get('router')->pathFor('rooms'));
    }
    
    // GET '/rooms/search'
    public function search($request, $response) {
        // redirect if the user isn't authenticated
        if ($this->current_user() == null) {
            return $response->withRedirect($this->ci->get('router')->pathFor('login'));
        }
        
        $query = '%' . $request->getParam('query') . '%';
        $rooms = Room::where('name', 'like', $query)
            ->where('password', null) // private rooms
            ->get();
        
        $locals = array_merge([
            'rooms' => $rooms
        ], $this->locals($request));
        
        return $this->renderer->render($response, 'search.twig', $locals);
    }
    
    // POST '/rooms/join'
    public function join($request, $response) {
        $room = Room::where('id', $request->getParam('room'))->first();
        $room->members()->create([
            'user_id' => $this->current_user()->id    
        ]);
        
        $this->flash->addMessage('success', 'You joined room ' . $room->name . '!');
        return $response->withRedirect($this->ci->get('router')->pathFor('rooms'));
    }
    
    private function createRoom($params) {
        if (!$this->validate($params)) {
            return null;
        }
        
        $room = new Room;
        $room->name = $params['name'];
        if ($params['password'] != null) {
            $room->password = $params['password'];
        }
        $room->owner_id = $this->current_user()->id;
        $room->save();
        
        $room->members()->create([
            'user_id' => $this->current_user()->id
        ]);
        return $room;
    }
    
    private function validate($params) {
        // unique constraint on name
        $count = Room::where('name', $params['name'])->count();
        if ($count != 0) {
            $this->is_invalid = true;
            $this->validation_error = 'Name already exists.';
            return false;
        }
        
        // confirmation password
        if ($params['password'] != null) {
            if ($params['password'] != $params['password_confirm']) {
                $this->is_invalid = true;
                $this->validation_error = 'Passwords do not match.';
                return false;
            }
        }
        return true;
    }
}