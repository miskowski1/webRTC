/*
$(document).ready(function() {

    var my_video = document.querySelector(".video-myself");
    var remote_video = document.querySelector(".video-caller");

    var connection = new (window.RTCPeerConnection ||
        window.mozRTCPeerConnection ||
        window.webkitRTCPeerConnection
    )();

    var connection2 = new (window.RTCPeerConnection ||
        window.mozRTCPeerConnection ||
        window.webkitRTCPeerConnection
        )();

    var connectionDescription = (window.RTCSessionDescription ||
        window.mozRTCSessionDescription ||
        window.webkitRTCSessionDescription
    );

    navigator.getUserMedia = (navigator.getUserMedia ||
        navigator.webkitGetUserMedia ||
        navigator.mozGetUserMedia ||
        navigator.oGetUserMedia ||
        navigator.msieGetUserMedia
    );

    if (navigator.getUserMedia) {
        navigator.getUserMedia (
        {
            video: true
//            audio: true
        },
            function (localMediaStream) {
                my_video.src = window.URL.createObjectURL(localMediaStream);
                */
/*connection.addStream(localMediaStream);
                connection.createOffer(function(offer) {
                    connection.setLocalDescription(offer);
                    connection2.createAnswer(function(answer) {
                        connection2.setRemoteDescription(offer);
                    }, error);
                }, error);
                connection2.onaddstream(function(e) {
                    remote_video.scr = window.URL.createObjectUrl(e.stream);
                });*//*

            },

            function(e) {
                alert(e);
            }
        );
    } else {
        alert('UserMedia not supported');
    }

    function error(err) {
        connection.close();
    }
});*/

var mediaConstraints = {
    optional: [],
    mandatory: {
    OfferToReceiveAudio: true,
    OfferToReceiveVideo: true
    }
};

var offerer, answerer;
var offererToAnswerer = document.getElementById('peer1-to-peer2');
var answererToOfferer = document.getElementById('peer2-to-peer1');

window.RTCPeerConnection = window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
window.RTCSessionDescription = window.mozRTCSessionDescription || window.RTCSessionDescription;
window.RTCIceCandidate = window.mozRTCIceCandidate || window.RTCIceCandidate;

navigator.getUserMedia = navigator.mozGetUserMedia || navigator.webkitGetUserMedia;
window.URL = window.webkitURL || window.URL;

window.iceServers = {
    iceServers: [{
    url: 'stun:23.21.150.121'
    }]
};

/* offerer */

function offererPeer(stream) {
    offerer = new RTCPeerConnection(window.iceServers);
    offerer.addStream(stream);

    offerer.onaddstream = function (event) {
    offererToAnswerer.src = URL.createObjectURL(event.stream);
    offererToAnswerer.play();
    };

offerer.onicecandidate = function (event) {
    if (!event || !event.candidate) return;
    answerer.addIceCandidate(event.candidate);
    };

offerer.createOffer(function (offer) {
    offerer.setLocalDescription(offer);
    answererPeer(offer, stream);
    }, onSdpError, mediaConstraints);
}


/* answerer */

function answererPeer(offer, stream) {
    answerer = new RTCPeerConnection(window.iceServers);
    answerer.addStream(stream);

    answerer.onaddstream = function (event) {
    answererToOfferer.src = URL.createObjectURL(event.stream);
    answererToOfferer.play();
    };

answerer.onicecandidate = function (event) {
    if (!event || !event.candidate) return;
    offerer.addIceCandidate(event.candidate);
    };

answerer.setRemoteDescription(offer, onSdpSucces, onSdpError);
answerer.createAnswer(function (answer) {
    answerer.setLocalDescription(answer);
    offerer.setRemoteDescription(answer, onSdpSucces, onSdpError);
    }, onSdpError, mediaConstraints);
}



function getUserMedia(callback) {
    navigator.getUserMedia({
        audio: true,
        video: true
    }, callback, onerror);

function onerror(e) {
    console.error(e);
    }
}

getUserMedia(function (stream) {
    offererPeer(stream);
    });

function onSdpError(e) {
    console.error('onSdpError', e);
    }

function onSdpSucces() {
    console.log('onSdpSucces');
    }