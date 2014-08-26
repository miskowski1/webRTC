(function () {
    Socket = function (url, room, user) {
        this.url = url;
        this.room = room;
        this.user = user;
        this.fullURL = url + '/?room=' + room + '&user=' + user;
        this.socket = undefined;
        this.handlers = {};
        that = this;
        this.wait = function (socket, callback) {
            setTimeout(
                function () {
                    if (socket.readyState === 1) {
                        if (callback != null) {
                            callback();
                        }
                    } else {
                        that.wait(socket, callback);
                    }
                },
                5 // wait 5 millisecond for the connection...
            );
        }
    };

    Socket.prototype = {
        connect: function () {
            this.socket = new WebSocket(this.fullURL);
            this.socket.onmessage = this.handleMessage;
        },
        send: function (type, message) {
            data = {
                'type': type,
                'payload' : message
            };
            console.log("<<<<<<", data);
            if (this.socket.readyState !== 1) {
                this.wait(this.socket, function() {
                    this.socket.send(JSON.stringify(data));
                });
            } else {
                this.socket.send(JSON.stringify(data));
            }
        },
        onmessage: function(event) {
            console.log(">>>>>", event);
        },
        on: function (type, callback) {
            this.handlers[type] = callback;
        },
        handleMessage: function(event) {
            var message = JSON.parse(event.data);
            if (message.hasOwnProperty('type')
                && that.handlers.hasOwnProperty(message.type)
            ) {
                return that.handlers[message.type](message.payload);
            }
            return that.onmessage(event);
        }
    };
}());
