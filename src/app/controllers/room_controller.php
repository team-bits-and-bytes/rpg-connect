<?php
namespace Controllers;

use Slim\Container;
use Models\Room;
use Models\Member;
use Lib\Cache;

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
        
        // detele messages
        $cache_key = $room->id . '_messages';
        $this->ci->get('redis')->jsonset($cache_key, null);
        
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
        
        return $this->renderer->render($response, 'rooms/search.twig', $locals);
    }
    
    // POST '/rooms/{id}/join'
    public function join($request, $response, $args) {
        $room = Room::where('id', $args['id'])->first();

        // if the room is private, check for a valid password
        if ($room->private == true) {
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
    
    // GET '/rooms/{id}'
    public function show($request, $response, $args) {
        // redirect if the user isn't authenticated
        if ($this->current_user() == null) {
            return $response->withRedirect($this->ci->get('router')->pathFor('root'));
        }
        
        $room = Room::where('id', $args['id'])->first();
        if (is_null($room)) {
            $this->flash->addMessage('error', 'Room does not exist.');
            return $resposne->withRedirect($this->ci->get('router')->pathFor('rooms'));
        }
        
        $locals = array_merge([
        ], $this->locals($request));    
        return $this->renderer->render($response, 'rooms/chat.twig', $locals);
    }
    
    // POST '/rooms/{id}/message'
    public function message($request, $response, $args) {
        $cache_key = $args['id'] . '_messages';
        $data = $request->getParams();
        
        // clean up the csrf values from the form
        unset($data['csrf_name']);
        unset($data['csrf_value']);

        $messages = $this->ci->get('redis')->jsonget($cache_key);
        
        // set the message id
        $msg_id = is_null($messages) ? 1 : count($messages) + 1;
        $data = array_merge([ 'message_id' => $msg_id ], $data);
        
        // if we already have messages for this CACHE_KEY, then push onto
        // the stack
        if (is_null($messages) == false) {
            array_push($messages, $data);
            $data = $messages;
            $this->ci->get('redis')->jsonset($cache_key, $data);
        } else {
            $this->ci->get('redis')->jsonset($cache_key, [$data]);
        }
        
        // TODO: Set expiry on key? How long?
        
        // CSRF is reset after a POST request, so send down new details
        $name = $request->getAttribute('csrf_name');
        $value = $request->getAttribute('csrf_value');
        return $response->withJson([ 'name' => $name, 'value' => $value ], 201);
    }
    
    // GET '/rooms/{id}/messages'
    public function messages($request, $response, $args) {
        // return nothing if not logged in
        if ($this->current_user() == null) {
            return $response->withJson([], 200);
        }
        
        // Return nothing if the user is not a member
        $room = Room::where('id', $args['id'])->first();
        if ($room->is_member == false) {
            return $response->withJson([], 200);
        }
        
        $cache_key = $room->id . '_messages';
        $data = $this->ci->get('redis')->jsonget($cache_key);
        if (is_null($data)) {
            $data = [];
        } else {
            // filter data to only include messages after the requested id.
            $id = $request->getParam('message_id');
            if (is_null($id) == false) {
                $filtered = [];
                foreach ($data as $message) {
                    if ($message->message_id > $id) {
                        array_push($filtered, $message);
                    }
                }
                $data = $filtered;
            }
        }
        return $response->withJson($data, 200);
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