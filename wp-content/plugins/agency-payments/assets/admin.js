/**
 * Agency Payments
 * ------------------------------------
 *
 *
 */

jQuery(document).ready(function($) {

	PaymentsAdmin = {

		init: function(){

			this.transactionOptions();

		},

		transactionOptions: function(){

			if($('.transaction-metabox').length <= 0) return;

			$('.transaction-metabox button').on('click',function(e){

				e.preventDefault();

				var $button = $(this);

				$button.text('Sending...');
				$button.attr('disabled','disabled');


                $.getJSON('/payments/api/receipt/send?tid=' + $button.data('tid'), function(data) {
                    if (data.success == true) {
                        $button.text('Sent');
                    }
                });

			});

		}



	}


	// ------------------------------------
	// GO
	// ------------------------------------

	// Export
	window.PaymentsAdmin = PaymentsAdmin;

	PaymentsAdmin.init();





});
