/* global $ */

$(document).ready(function() {
    $('.dice > a').on('click', function() {
        var num = $(this).data('num');
        var die = $(this).data('die');
        
        var url = 'https://rolz.org/api/?' + num + 'd' + die + '.jsonp';
        
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'jsonp', // hack for Origin error
        }).done(function(response) { 
            var id = $('.messages').find('input[name="user_id"]')[0].value;
            var name = $('.messages').find('input[name="user_name"]')[0].value;
            
            window.rpgc.sendMessage({
               from: {
                   id: id,
                   name: name,
               },
               message: 'I rolled ' + response.result + ' with ' + response.input + response.details
            });
        });
    });
});