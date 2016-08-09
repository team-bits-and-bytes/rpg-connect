<?php

namespace Controllers;

use Models\Note;

class NoteController extends BaseController {
    // POST /note
    public function create($request, $response) {
        // does a record for this user already exist?
        $note = Note::where('user_id', $this->current_user()->id)->first();
        if (is_null($note)) {
            $note = new Note;
            $note->user_id = $this->current_user()->id;
        }
        
        $note->body = $request->getParam('body');
        $note->save();
        
        $this->flash->addMessage('success', 'Your note has been saved!');
        return $response->withRedirect($this->ci->get('router')->pathFor('root'));
    }
    
    // GET /note/delete
    // DELETE /note/delete
    public function delete($request, $response) {
        $note = Note::where('user_id', $this->current_user()->id)->first();
        if (is_null($note)) {
            $this->flash->addMessage('error', 'Note does not exist.');
            return $response->withRedirect($this->ci->get('router')->pathFor('root'));
        }
        
        $note->delete();
        $this->flash->addMessage('success', 'Your note has been deleted.');
        return $response->withRedirect($this->ci->get('router')->pathFor('root'));
    }
    
    // GET '/note/download'
    public function download($request, $response) {
        $note = Note::where('user_id', $this->current_user()->id)->first();
        if (is_null($note)) {
            $this->flash->addMessage('error', 'Note does not exist.');
            return $response->withRedirect($this->ci->get('router')->pathFor('root'));
        }
        
        $file_name = $this->current_user()->username . '_note.txt';
        $response->getBody()->write($note->body);
        return $response->withHeader('Content-Disposition', 'attachment; filename=' . $file_name)
            ->withHeader('Content-Type', 'text/plain')
            ->withStatus(200);
    }
}