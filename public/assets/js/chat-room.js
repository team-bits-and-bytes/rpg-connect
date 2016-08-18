/* global $ */

$(document).ready(function() {
    var LAST_MESSAGE_ID = 0;
    
    // create HTML markup
    var createMessage = function(data) {
        // does this message already exist? (could be race condition bug)
        if ($('[data-message-id="' + data.message_id + '"]').length > 0) {
            return;
        }
        
        var $ul = $('#messagedisplay > ul');
        var $li = $('<li/>');
        
        var $user = $('<span class="user"/>');
        $user.append(data.from.name);
        
        var $message = $('<p/>');
        $message.append(data.message);
        
        var $div = $('<div class="message" data-message-id="' + data.message_id + '"' + '/>');
        $div.append($user);
        $div.append($message);
        
        $li.append($div);
        $ul.append($li);
    };
    
    // send messages upstream
    var sendMessage = function(data, callback) {
        var url = window.location.pathname + '/message';
        var csrf_name = $('input[name="csrf_name"]').val();
        var csrf_value = $('input[name="csrf_value"]').val();
        
        data['csrf_name'] = csrf_name;
        data['csrf_value'] = csrf_value;
        
        $.ajax({
            method: 'POST',
            url: url,
            data: data
        }).done(function(response) {
            // update CSRF values
            $('input[name="csrf_name"]').val(response.name);
            $('input[name="csrf_value"]').val(response.value);
            if (callback !== undefined && typeof callback === 'function') {
                callback();
            }
        });
    };
    
    var getMessages = function(callback) {
        var url = window.location.pathname + '/messages';
        $.ajax({
            method: 'GET',
            url: url,
            data: { 'message_id': LAST_MESSAGE_ID }
        }).done(function(response) {
            if (response.length !== 0) {
                // get last message in response array and update LAST_MESSAGE_ID
                LAST_MESSAGE_ID = response[response.length - 1]['message_id'];
                $.each(response, function(_, message) {
                   createMessage(message);
                });
            }
            
            // scroll to bottom
            $('#messagedisplay').scrollTop($('#messagedisplay').prop('scrollHeight'));
            
            // execute callback if it exists!
            if ((callback !== undefined || callback !== null) && typeof callback === 'function') {
                callback();
            }
        });
    };
   
    // sending a new message
    $('#chatroom form').on('submit', function(e) {
        e.preventDefault();
        
        // get value from input element
        var value = $(this).find('#chattext')[0].value;
        if (value === '') {
          return;
        }
        var id = $(this).find('input[name="user_id"]')[0].value;
        var name = $(this).find('input[name="user_name"]')[0].value;
        var data = { from: { id: id, name: name }, message: value };
        
        // send data upstream
        sendMessage(data);
        
        // reset the input
        $(this).find('#chattext')[0].value = '';
    });
    
    // request old messages instantly
    getMessages(function() {
        // constantly request new info from the server!
        setInterval(function() {
            getMessages();
        }, 500); // every 2s
    });
    
    // expose `sendMessage` as a global so other JavaScript files may use it
    window.rpgc = window.rpgc || {};
    window.rpgc.sendMessage = sendMessage;
});