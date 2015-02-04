jQuery(document).ready(function($) {

	var Theme = {
		init: function() {
			this.share();
		},

		share: function() {
			$('.share').share({
				counts: true,
				threshold: 0,
				abbreviate: true
			});
		}

	}

	Theme.init();

	window.Theme = Theme;

});
