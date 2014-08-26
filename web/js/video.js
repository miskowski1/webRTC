+function ($) {
    var RtcVideo;
    RtcVideo = function (el) {
        /**
         * Viedo element
         */
        this.el = null;

        /**
         * Status video (is fullscreen or not)
         */
        this.fullscreen = null;

        /**
         * button to make fullscreen
         */
        this.fullscreenButton = null;

        /**
         * Browser prefix
         */
        this.fullscreenMethod = null;

        this.init(el);
    };

    /**
     * Init prototype
     * @type {RtcVideo}
     */
    RtcVideo.prototype.constructor = RtcVideo;

    /**
     * Prototype init method
     * @param el
     */
    RtcVideo.prototype.init = function (el) {
        this.el = el;
        this.fullscreen = false;
        this.fullscreenButton = $('#fullscreen');

        this.fullscreenButton.live('click', function(e){
            e.preventDefault();
        });
    }

    $.fn.rtcVideo = function(option, _relatedTarget) {
        return this.each(function(){
            data = $(this).data('rtc.video');
            if (!data) $(this).data('rtc.video', (data = new RtcVideo(this)));
        });
    }
}(jQuery);