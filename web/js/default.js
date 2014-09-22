'use strict';

(function($){
    $(document).ready(function () {
        $('.start-conference').on('click', function (e) {
            e.preventDefault();
            if (!$(this).data('started')) {
                $(this).text('Połączono').attr('disabled', true);
            }
        });
        $('#fullscreen').on('click', function(){
            var $mainVideo = $('#main-video').get(0);
            switch (webrtcDetectedBrowser) {
                case "firefox":
                    $mainVideo.mozRequestFullScreen();
                    break;
                case "chrome":
                    $mainVideo.webkitRequestFullscreen();
                    break;
            }
        });
    });
})(jQuery);