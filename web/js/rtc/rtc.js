'use strict';
function clone(obj) {
    if (null == obj || "object" != typeof obj) return obj;
    var copy = obj.constructor();
    for (var attr in obj) {
        if (obj.hasOwnProperty(attr)) copy[attr] = clone(obj[attr]);
    }
    return copy;
}

var localStream;
var peerConnections = [];
var participants = [];

var socket = new Socket(settings.url, settings.port, settings.room, settings.user);
socket.connect();
socket.send('room', 'ALL');

socket.on('room', function (data) {
    participants = clone(data);
});

socket.on('new', function (data) {
    for (var id in data) {
        if (data.hasOwnProperty(id)) {
            participants[id] = data[id];
        }
    }
});

socket.on('disconnect', function (data, message) {
    var user = message.user;
    if (participants.hasOwnProperty(user)) {
        delete participants[data];
    }
    if (peerConnections.hasOwnProperty(user)) {
        var peer = peerConnections[user];
        peer.close();
        delete peerConnections[user];
    }
    removeCameraStream(user);
});

socket.on('connect', function (data) {
    if (data.length > 0) {
        for (var i = 0; i < data.length; i++) {
            var peer = Peer();
            peer.user = data[i];
            peer.name = participants[data[i]];
            peer.addStream(localStream);
            peerConnections[data[i]] = peer;
            initiateConnection(peer);
        }
    }
});

function initiateConnection(peer) {
    peer.createOffer(function (sessionDescription) {
        peer.setLocalDescription(sessionDescription, function () {
            socket.send('offer', peer.localDescription, peer.user);
        });
    }, function (arg) {
        console.log(arg);
    }, PeerConfig.sdp);
}

socket.on('conference', function (data) {
    if (typeof data == 'string') {
        switch (data) {
            case 'READY':
                //All are there, but not started
                break;
            case 'EMPTY':
                //No one else is in the room
                break;
            case 'NOLEADER':
                //Leader is absent
                break;
        }
    }
});


socket.on('offer', function (data, message) {
    if (typeof message.user != 'undefined') {
        var peer;
        if (message.user in peerConnections) {
            delete peerConnections[message.user];
        }
        peer = Peer();
        peer.user = message.user;
        peer.name = participants[message.user];
        peer.addStream(localStream);
        peerConnections[message.user] = peer;
        var offer = new RTCSessionDescription(data);
        peer.setRemoteDescription(offer, function () {
            makeAnswer(peer);
        });
    }
});

socket.on('answer', function (data, message) {
    if (typeof message.user != 'undefined') {
        if (message.user in peerConnections) {
            var peer = peerConnections[message.user];
            var answer = new RTCSessionDescription(data);
            peer.setRemoteDescription(answer, function () {
                if (peer.getRemoteStreams().length > 0) {
                    //addCameraStream(peer.getRemoteStreams()[0], peer.user, peer.name);
                }
            });
        }
    }
});

function makeAnswer(peer) {
    peer.createAnswer(function (sessionDescription) {
        peer.setLocalDescription(sessionDescription, function () {
            socket.send('answer', peer.localDescription, peer.user);
        });
    }, function (arg) {
        console.log(arg);
    }, PeerConfig.sdp);
    /*if (peer.getRemoteStreams().length > 0) {
        addCameraStream(peer.getRemoteStreams()[0]);
    }*/
}

socket.on('candidate', function (data, message) {
    if (typeof message.user != 'undefined') {
        if (message.user in peerConnections) {
            var peer = peerConnections[message.user];
            var candidate = new RTCIceCandidate({
                sdpMLineIndex: data.label,
                candidate: data.candidate
            });
            peer.addIceCandidate(candidate);
        }
    }
});


function handleUserMedia(stream) {
    localStream = stream;
    addCameraStream(stream, "YOU", settings.user);
    socket.send('media', 'READY');
}

function handleUserMediaError(error) {
    //Error message
    console.log('getUserMedia error: ', error);
}

var constraints = {
    video: true/*{
        mandatory: {
            maxWidth: 600,
            maxHeight: 400
        }
    }*/,
    audio: true
};

getUserMedia(constraints, handleUserMedia, handleUserMediaError);
console.log('Getting user media with constraints', constraints);


socket.on('bye', function (data, message) {
    console.log("Got Bye");
    console.log(message);

});

window.onbeforeunload = function(){
    socket.send('BYE', 'LEAVING');
    socket.socket.close();
};

function startConference() {
    socket.send('CONNECT', 'ALL');
}