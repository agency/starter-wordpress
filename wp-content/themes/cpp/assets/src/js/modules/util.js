// ------------------------------------
//
// Utilities
//
// ------------------------------------

(function($) {

	if (typeof window.Util == 'undefined') window.Util = {};

	Util = {

		// ------------------------------------
		// Util Init
		// ------------------------------------

		init: function() {

			console.log('Util::init()');

		},

        isMobile: function() {
            return $(window).width() < 768;
        },
        
        isTablet: function() {
            return $(window).width() < 1024
        },
        // ------------------------------------
        // CSS Helpers
        // ------------------------------------

        detectBrowser: function(){

            if (!$.browser) return;

            // Firefox
            if ($.browser.mozilla) { $('html').addClass('firefox'); }

            // Chrome
            if ($.browser.chrome) { $('html').addClass('chrome'); }

            // Safari
            if ($.browser.safari) { $('html').addClass('safari'); }

            // IE
            if ($.browser.msie) { $('html').addClass('ie'); }
            if (!!navigator.userAgent.match(/Trident\/7\./))  { $('html').addClass('ie'); }

            // OS
            var os = window.navigator.platform.toLowerCase();

            // Windows
            if(os.indexOf('win') >= 0) { $('html').addClass('windows'); }
            else if(os.indexOf('mac') >= 0) { $('html').addClass('mac'); }

            // IOS
            if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)){  $('html').addClass('ios'); }

            // IE Version
            var ua = window.navigator.userAgent;
            var msie = ua.indexOf('MSIE ');
            if (msie > 0) {
                // IE 10 or older => return version number
                var version = parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
                $('html').addClass('ie'+version);

            }

            var trident = ua.indexOf('Trident/');
            if (trident > 0) {
                // IE 11 => return version number
                var rv = ua.indexOf('rv:');
                var version =  parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
                $('html').addClass('ie'+version);
            }

            var edge = ua.indexOf('Edge/');
            if (edge > 0) {
                // Edge (IE 12+) => return version number
                var version = parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
                $('html').addClass('ie'+version);
            }

        }

	};

	module.exports = Util;

})(jQuery);