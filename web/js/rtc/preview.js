'use strict';

var cameras = $('.cameras > ul');
var prototype = '' +
    '<li id="user-camera-{{ID}}" class="ui-state-default">'+
        '<video autoplay muted></video>'+
        '<div class="info">{{NAME}}</div>'+
    '</li>';
var mainCamera = $('#main-video').get(0);

function addCameraStream(stream, name, ID) {
    var element = prototype
        .replace('{{NAME}}', name)
        .replace('{{ID}}', ID);
    $(element).appendTo(cameras);
    var video = $('.cameras li:last-child > video').get(0);
    attachMediaStream(video, stream);
    video.muted = true;
}

cameras.on('click', 'li', function(e){
    e.preventDefault();
    reattachMediaStream(mainCamera, $(this).children('video').get(0));
});

function removeCameraStream(ID) {
    $('#user-camera-' + ID).remove();
}