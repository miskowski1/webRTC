'use strict';

var chat = $('.chat');
var chatprototype = '<div>' +
    '<span class="username">{{USERNAME}}</span>' +
    '<span class="message">{{MESSAGE}}</span>' +
    '</div>';

var input = $('#message');

function addMessage(username, message) {
    var el = chatprototype
        .replace('{{USERNAME}}', username)
        .replace('{{MESSAGE}}', message);
    $(el).appendTo(chat);
}

function sendMessage(message) {
    socket.send('chat', message);
}

socket.on('chat', function(data) {
    addMessage(data.user, data.message);
});

$('#send-message').on('click', function(){
    if ( input.val() != '') {
        sendMessage(input.val());
        input.val('');
    }
});