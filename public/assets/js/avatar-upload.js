/* global $ */

$(document).ready(function() {
    // when the file input changes, send us the data
    $("#avatar_upload").on('change', function(event) {
        var input = event.target;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            // update the hidden input field within the form with our data-uri
            reader.onload = function(evt) {
                $('input[name="avatar"]').val(evt.target.result);
                // Update the avatar image to show newly selected one
                $('#current_avatar').prop('src', evt.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    });
});