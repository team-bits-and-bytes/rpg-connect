/* global $ */

$(document).ready(function() {
    // if the join form exists, listen to the submit event
    $('#join_room').on('submit', function(e) {
        // if the room is private, ask for a password!
        var private = $(this).data('private');
        if (private === 1) {
            var result = window.prompt('Enter the room password');
            $(this).find('input[name="password"]').val(result);
        }
    });
});