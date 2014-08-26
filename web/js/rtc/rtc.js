'use strict';

var localVideo = document.querySelector('#localVideo');
var remoteVideo = document.querySelector('#remoteVideo');
var isStarted = false;
var isChannelReady = false;
var isInitiator = false;
var localStream;
var peerConnection;
var channel;

var socket = new Socket("ws://10.0.69.28:8080", "wertgh", "wergtf45");
socket.connect();

var pc_config =
{'iceServers': [
    {url: "stun:23.21.150.121"},
    {url: "stun:stun.l.google.com:19302"},
    {url: "turn:numb.viagenie.ca", credential: "webrtcdemo", username: "louis%40mozilla.com"}
]};

var pc_constraints = {
    'optional': [
        {'DtlsSrtpKeyAgreement': true},
        {'RtpDataChannels': true}
    ]};

// Set up audio and video regardless of what devices are present.
var sdpConstraints = {'mandatory': {
    'OfferToReceiveAudio': true,
    'OfferToReceiveVideo': true }};

function handleUserMedia(stream) {
    localStream = stream;
    attachMediaStream(localVideo, stream);
    console.log('Adding local stream.');
    socket.send('media', 'ready');
    if (isInitiator) {
        start();
    }
}

function handleUserMediaError(error) {
    console.log('getUserMedia error: ', error);
}

var constraints = {
    video: true,
    audio: true
};

getUserMedia(constraints, handleUserMedia, handleUserMediaError);
console.log('Getting user media with constraints', constraints);

function handleIceCandidate(event) {
    if (event.candidate) {
        socket.send(
            'candidate',
            {
                label: event.candidate.sdpMLineIndex,
                id: event.candidate.sdpMid,
                candidate: event.candidate.candidate
            }
        );
    } else {
        console.log('End of candidates.');
    }
}

socket.on('media', function (data) {
    //@TODO check READY|START|LEADER_ERROR|WATCHER_ERROR
    start();//@TODO
});

socket.on('offer', function (data) {
    if (!isInitiator && !isStarted) {
        start(); //@TODO
    }
    var offer = new RTCSessionDescription(data);
    peerConnection.setRemoteDescription(offer, makeAnswer);
});

socket.on('answer', function (data) {
    if (isStarted) {
        var answer = new RTCSessionDescription(data);
        peerConnection.setRemoteDescription(answer, function () {
            if (peerConnection.getRemoteStreams().length > 0) {
                attachMediaStream(remoteVideo, peerConnection.getRemoteStreams()[0]);
            }
        });
    }
});

socket.on('candidate', function (data) {
    if (isStarted) {
        console.log(message);
        var candidate = new RTCIceCandidate({
            sdpMLineIndex: data.label,
            candidate: data.candidate
        });
        peerConnection.addIceCandidate(candidate);
    }
});

socket.on('bye', function (data) {
    if (isStarted) {
        /*peerConnection.close();
         peerConnection = null;
         isStarted = false;
         isInitiator = false;*/
    }
});

//@TODO change this
socket.onmessage = function (e) {
    var message = JSON.parse(e.data);
    if (message.key === 'join') {
        isChannelReady = true;
        start();
    }
};
/*window.onbeforeunload = function(e){
 sendMessage('bye');
 };*/


function start() {
    console.log("Starting up");
    if (!isStarted && localStream && isChannelReady) {
        try {
            peerConnection = new RTCPeerConnection(pc_config, pc_constraints);
            peerConnection.onicecandidate = handleIceCandidate;
            console.log('Created RTCPeerConnnection with:\n' +
                '  config: \'' + JSON.stringify(pc_config) + '\';\n' +
                '  constraints: \'' + JSON.stringify(pc_constraints) + '\'.');
        } catch (e) {
            console.log('Failed to create PeerConnection, exception: ' + e.message);
            alert('Cannot create RTCPeerConnection object.');
            return;
        }
        peerConnection.onaddstream = handleRemoteStreamAdded;
        peerConnection.addStream(localStream);
        isStarted = true;
        if (isInitiator) {
            call();
        }
    }
}

function handleRemoteStreamAdded() {
    console.log('Remote stream added.');
    // reattachMediaStream(miniVideo, localVideo);
    attachMediaStream(remoteVideo, event.stream);
    var remoteStream = event.stream;
//  waitForRemoteVideo();
}

function call() {
    peerConnection.createOffer(function (sessionDescription) {
        peerConnection.setLocalDescription(sessionDescription, function () {
            socket.send('offer', peerConnection.localDescription);
        });
    }, function (arg) {
        console.log(arg);
    }, sdpConstraints);
}

function makeAnswer() {
    console.log("Made answer");
    peerConnection.createAnswer(function (sessionDescription) {
        peerConnection.setLocalDescription(sessionDescription, function () {
            socket.send('answer', peerConnection.localDescription);
        });
    }, function (arg) {
        console.log(arg);
    }, sdpConstraints);
    if (peerConnection.getRemoteStreams().length > 0) {
        attachMediaStream(remoteVideo, peerConnection.getRemoteStreams()[0]);
    }
}

