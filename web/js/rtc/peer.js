'use strict';
/**
 * Configuration for RTCPeerConnection
 * @type {{ice: {iceServers: *[]}, constraints: {optional: *[]}}}
 */
var PeerConfig = { 'ice': {'iceServers': [
    {url: "stun:23.21.150.121"},
    {url: "stun:stun.l.google.com:19302"},
    {url: 'stun:stun01.sipphone.com'},
    {url: 'stun:stun.ekiga.net'},
    {url: 'stun:stun.fwdnet.net'},
    {url: 'stun:stun.ideasip.com'},
    {url: 'stun:stun.iptel.org'},
    {url: 'stun:stun.rixtelecom.se'},
    {url: 'stun:stun.schlund.de'},
    {url: 'stun:stun.l.google.com:19302'},
    {url: 'stun:stun1.l.google.com:19302'},
    {url: 'stun:stun2.l.google.com:19302'},
    {url: 'stun:stun3.l.google.com:19302'},
    {url: 'stun:stun4.l.google.com:19302'},
    {url: 'stun:stunserver.org'},
    {url: 'stun:stun.softjoys.com'},
    {url: 'stun:stun.voiparound.com'},
    {url: 'stun:stun.voipbuster.com'},
    {url: 'stun:stun.voipstunt.com'},
    {url: 'stun:stun.voxgratia.org'},
    {url: 'stun:stun.xten.com'},
    {
        url: 'turn:numb.viagenie.ca',
        credential: 'muazkh',
        username: 'webrtc@live.com'
    },
    {
        url: 'turn:192.158.29.39:3478?transport=udp',
        credential: 'JZEOEt2V3Qb0y27GRntt2u2PAYA=',
        username: '28224511:1379330808'
    },
    {
        url: 'turn:192.158.29.39:3478?transport=tcp',
        credential: 'JZEOEt2V3Qb0y27GRntt2u2PAYA=',
        username: '28224511:1379330808'
    },
    {url: "turn:numb.viagenie.ca", credential: "webrtcdemo", username: "louis%40mozilla.com"}
]},
    'constraints': {
        'optional': [
            {'DtlsSrtpKeyAgreement': true},
            {'RtpDataChannels': true}
        ]
    },
    'sdp': {'mandatory': {
        'OfferToReceiveAudio': true,
        'OfferToReceiveVideo': true }
    }
};

/**
 * Peer wrapper for multipeer with user
 * @constructor
 */
var Peer = function () {
    var peer = new RTCPeerConnection(PeerConfig.ice, PeerConfig.constraints);
    peer.user = null;
    peer.name = null;

    peer.onicecandidate = function handleIceCandidate(event) {
        if (event.candidate) {
            socket.send(
                'candidate',
                {
                    label: event.candidate.sdpMLineIndex,
                    id: event.candidate.sdpMid,
                    candidate: event.candidate.candidate
                },
                this.user
            );
            this.onicecandidate = undefined;
        } else {
            console.log('End of candidates.');
        }
    };
    peer.onaddstream = function (event) {
        console.log('Remote stream added for ' + this.user + ' ' + this.name);
        addCameraStream(event.stream, this.name, this.user)
    };
    return peer;
};