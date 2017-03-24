/*	
 * Theme Javascript Initializer
 */

require('./plugins/share');
// require('./plugins/share-count');
require('./plugins/smart-resize');
// require('./plugins/stroke');

// require('owl.carousel');


var Util  		= require('./modules/util');
var Theme 		= require('./modules/theme');
var Maps 		  = require('./modules/maps');

// var Map   		= require('./modules/map');
// var Form   		= require('./modules/form');
// var VideoManager   	= require('./modules/video-manager');

jQuery(document).ready(function($) {

	Util.detectBrowser(); // Adds Browser Version and OS as a class to HTML
	Theme.init(); // Start the Theme
	Maps.init();

	// let videoManager = new VideoManager($('.video-overlay'), $('.js-video-play'));
	// videoManager.init(Util);


	// // Init Maps
	// if ($('.g-map').length > 0) {
	// 	$('.g-map').each((i, el) => {
			
	// 		// create map
	// 		let map = new Map($(el));

	// 	});
	// }


	// if ($('.form').length > 0) {
	// 	$('.form').each((i, el) => {
			
	// 		// create map
	// 		let form = new Form($(el));

	// 	});
	// }

});