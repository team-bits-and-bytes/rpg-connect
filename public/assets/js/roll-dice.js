/* global $ */

var die = [4, 6, 8, 10, 12, 20];
var stack = [];

$(document).ready(function() {
    var getResults = function(num, die, target, callback) {
        var url = 'https://rolz.org/api/?' + num + 'd' + die + '.jsonp';
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'jsonp'
        }).done(function(response) {
            var id = $('#newmessage').find('input[name="user_id"]')[0].value;
            var name = $('#newmessage').find('input[name="user_name"]')[0].value;
            
            // calculate number of success rolls
            var total = 0;
            var nums = response.details.split(' +');
            for (var i = 0; i < nums.length; i++) {
                var str = nums[i].replace(' (', '');
                str = str.replace(') ', '');
                var num = parseInt(str, 10);
                if (num >= target) {
                    total = total + 1;
                }
            }
            
            if (target === undefined || target === '') {
                target = 'None';
            }
            
            var details = response.details.replace(' (', '').replace(') ', '');
            
            window.rpgc.sendMessage({
               from: {
                   id: id,
                   name: name,
               },
               message: 'I rolled ' + response.input + ' (Target: ' + target + '). ' + total + ' successes. (Dice Results: ' + details + ')'
            }, function() {
                if (callback !== undefined && typeof callback === 'function') {
                    callback();
                }
            });
        });
    };
    
    $('#postdicebtn').on('click', function() {
        var customAmount = $('input[name="dCa"]').val();
        var customDie = $('input[name="dCn').val();
        var customTarget = $('input[name="dCt"]').val();
        if ((customAmount !== undefined && customAmount !== '') &&
            (customDie !== undefined && customDie !== '')) {
                stack.push({ num: customAmount, die: customDie, target: customTarget });  
            }
        
        
        // static dice
        for (var i = 0; i < die.length; i++) {
            var amount = $('input[name="d' + die[i] + 'a"]').val();
            if (amount === '' || amount === undefined || amount === null) {
                continue;
            }
            var target = $('input[name="d' + die[i] + 't"]').val();
            stack.push({ num: amount, die: die[i], target: target });
        }
        
        // each roll has to be done one at a time due the CSRF results changing
        // after each POST request..
        stack = stack.reverse();
        var callback = function() {
            var obj = stack.pop();
            if (obj === undefined) {
                return;
            }
            getResults(obj.num, obj.die, obj.target, callback);  
        };
        var first = stack.pop();
        if (first !== undefined) {
            getResults(first.num, first.die, first.target, callback);
        }
    });
    
    // clear dice fields
    $('#cleardicebtn').on('click', function() {
        for (var i = 0; i < die.length; i++) {
            $('input[name="d' + die[i] + 'a"]')[0].value = '';
            $('input[name="d' + die[i] + 't"]')[0].value = '';
            $('input[name="dCa"]')[0].value = '';
            $('input[name="dCn"]')[0].value = '';
            $('input[name="dCt"]')[0].value = '';
        }
    });
});