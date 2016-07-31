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
            return $response->withRedirect($this->ci->get('router')->pathFor('root'));
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
    
    // POST '/rooms/{id}/delete'
    // DELETE '/rooms/{id}/delete'
    public function delete($request, $response, $args) {
        $room = Room::where('id', $args['id'])->first();

        // error if the signed in user is not the owner of the room
        if ($this->current_user() != $room->owner) {
            $this->flash->addMessage('error', 'You are not the owner of this room.');
            return $response->withRedirect($this->ci->get('router')->pathFor('rooms'));
        }
        
        // delete member records!
        $members = $room->members();
        $members->each(function($member) {
            $member->delete();    
        });
        
        // delete room
        $room->delete();
        $this->flash->addMessage('success', 'Room was successfully deleted.');
        return $response->withRedirect($this->ci->get('router')->pathFor('rooms'));
    }
    
    // GET '/rooms/search'
    public function search($request, $response) {
        // redirect if the user isn't authenticated
        if ($this->current_user() == null) {
            return $response->withRedirect($this->ci->get('router')->pathFor('root'));
        }
        
        $query = '%' . $request->getParam('query') . '%';
        $rooms = Room::where('name', 'like', $query)->get();
        
        $locals = array_merge([
            'rooms' => $rooms
        ], $this->locals($request));
        
        return $this->renderer->render($response, 'search.twig', $locals);
    }
    
    // POST '/rooms/{id}/join'
    public function join($request, $response, $args) {
        $room = Room::where('id', $args['id'])->first();

        // if the room is private, check for a valid password
        if (is_null($room->password) == false) {
            if (is_null($request->getParam('password')) == false) {
                // confirm that the password is correct
                $result = password_verify($request->getParam('password'), $room->password);
                if ($result == false) {
                    $this->flash->addMessage('error', 'Incorrect password.');
                    return $response->withRedirect($this->ci->get('router')->pathFor('rooms'));
                }
            } else {
                $this->flash->addMessage('error', 'You must provide a password!');
                return $response->withRedirect($this->ci->get('router')->pathFor('rooms'));
            }
        }
        
        $room->members()->create([
            'user_id' => $this->current_user()->id
        ]);
        
        $this->flash->addMessage('success', 'You joined room ' . $room->name . '!');
        return $response->withRedirect($this->ci->get('router')->pathFor('rooms'));
    }
    
    // POST '/rooms/{id}/favourite'
    public function favourite($request, $response, $args) {
        $member = Member::where('room_id', $args['id'])
            ->where('user_id', $this->current_user()->id)
            ->first();
        if (is_null($member)) {
            $this->flash->addMessage('error', 'You are not a member of this room.');
            return $response->withRedirect($this->ci->get('router')->pathFor('rooms'));
        }
        
        $member->favourite = $request->getParam('favourite') == 'true' ? true : false;
        $member->save();
        
        $message = 'You ' . ($member->favourite ? 'favourited' : 'unfavourited') . ' ' . $member->room()->first()->name . '.';
        $this->flash->addMessage('success', $message);
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