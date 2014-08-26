'use strict';

var localVideo = document.querySelector('#localVideo');
var remoteVideo = document.querySelector('#remoteVideo');
var isStarted = false;
var isChannelReady = false;
var isInitiator = false;
var localStream;
var peerConnection;
var channel;

var socket = new WebSocket("ws://10.0.69.28:8080");

var pc_config =
{'iceServers':[
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
    'OfferToReceiveAudio':true,
    'OfferToReceiveVideo':true }};

function handleUserMedia(stream) {
    localStream = stream;
    attachMediaStream(localVideo, stream);
    console.log('Adding local stream.');
    sendMessage('media');
    if (isInitiator) {
        start();
    }
}

function handleUserMediaError(error){
    console.log('getUserMedia error: ', error);
}

var constraints = {video: true};

getUserMedia(constraints, handleUserMedia, handleUserMediaError);
console.log('Getting user media with constraints', constraints);

function waitForSocketConnection(socket, callback){
    setTimeout(
        function () {
            if (socket.readyState === 1) {
                if(callback != null){
                    callback();
                }
                return;

            } else {
                waitForSocketConnection(socket, callback);
            }

        }, 5); // wait 5 milisecond for the connection...
}

socket._send = socket.send;
//General send
socket.send = function(arg) {
    waitForSocketConnection(socket, function() {
        socket._send(JSON.stringify(arg));
    })
};

//Message send
function sendMessage(message) {
    var data = {
        key: 'message',
        data: message
    };
    socket.send(data);
}
function handleIceCandidate(event) {
    console.log(event);
    if (event.candidate) {
        sendMessage({
            type: 'candidate',
            label: event.candidate.sdpMLineIndex,
            id: event.candidate.sdpMid,
            candidate: event.candidate.candidate});
    } else {
        console.log('End of candidates.');
    }
}

//Receive
socket.onmessage = function(e) {
    var message = JSON.parse(e.data);
    if ( message.key === 'message' ) {
        message = message.data;
        //console.log(message);
        if ( message === 'media' ) {
            start();//@TODO
        } else if ( message.type === 'offer' ) {
            if (!isInitiator && !isStarted) {
                start(); //@TODO
            }
            var offer = new RTCSessionDescription(message);
            peerConnection.setRemoteDescription(offer, makeAnswer);
        } else if (message.type === 'answer' && isStarted) {
            var answer = new RTCSessionDescription(message);
            peerConnection.setRemoteDescription(answer, function() {
                if ( peerConnection.getRemoteStreams().length > 0 ) {
                    attachMediaStream(remoteVideo, peerConnection.getRemoteStreams()[0]);
                }
            });
        } else if (message.type === 'candidate' && isStarted) {
            console.log(message);
            var candidate = new RTCIceCandidate({
                sdpMLineIndex:message.label,
                candidate:message.candidate
            });
            peerConnection.addIceCandidate(candidate);
        } else if ( message === 'bye' && isStarted ) {
            /*peerConnection.close();
            peerConnection = null;
            isStarted = false;
            isInitiator = false;*/
        }
    } else if ( message.key === 'join' ) {
        isChannelReady = true;
        start();
    }
};
/*window.onbeforeunload = function(e){
    sendMessage('bye');
};*/



function start() {
    console.log("Starting up");
    if ( !isStarted && localStream && isChannelReady ) {
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
};

function call() {
    peerConnection.createOffer( function(sessionDescription) {
        peerConnection.setLocalDescription(sessionDescription, function() {
            sendMessage(peerConnection.localDescription);
        });
    }, function(arg) {console.log(arg);}, sdpConstraints);
}

function makeAnswer() {
    console.log("Made answer");
    peerConnection.createAnswer( function(sessionDescription) {
        peerConnection.setLocalDescription(sessionDescription, function() {
            sendMessage(peerConnection.localDescription);
        });
    }, function(arg) {console.log(arg);}, sdpConstraints);
    if ( peerConnection.getRemoteStreams().length > 0 ) {
        attachMediaStream(remoteVideo, peerConnection.getRemoteStreams()[0]);
    }
}

