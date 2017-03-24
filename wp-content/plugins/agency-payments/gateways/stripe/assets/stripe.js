jQuery(document).ready(function($) {

	// var GatewayStripe = {
	Payments.gateway.stripe = {

		name: 'Stripe',

	    init: function(){

	    	console.log("Stripe Gateway Enabled");

	    },

	    process: function( card ){

	    	console.log("Process Stripe Donation");

            if(card.savedCard == true){

                console.log(" SAVED CARD ");

                // Saved Card
                var $form = $('.payments-form');
                $form.find('[name="payment[card][number]"]').attr('disabled','disabled');
                $form.find('[name="payment[card][expiry]"]').attr('disabled','disabled');
                $form.find('[name="payment[card][cvc]"]').attr('disabled','disabled');
                $form.find('[name="payment[card][name]"]').attr('disabled','disabled');

                // Set Gateway
                $form.append($('<input type="hidden" name="payment[gateway]">').val('stripe'));

                // Submit Form
                $form.trigger('submit');
                return;

            }

			// Get Token for New Card
			Stripe.card.createToken(card, function(status, response) {

                console.log("TOKEN CREATION");

            	if(response.error){

            		// Error
            		console.log("ERROR: Stripe Token");
            		console.log("-- " + response.error.message);

            		// Display Error
            		Payments.form.throwError(response.error.message);

            		return false;

            	} else {

            		// Success
            		console.log("SUCCESS: Stripe Token");

            		// return response.id
            		var $form = $('.payments-form');

            		// Append Token To Form
            		$form.append($('<input type="hidden" name="payment[token]">').val(response.id));
                    $form.append($('<input type="hidden" name="payment[gateway]">').val('stripe'));

                    // Disable Fields so they dont post
                    $form.find('[name="payment[card][number]"]').attr('disabled','disabled');
                    $form.find('[name="payment[card][expiry]"]').attr('disabled','disabled');
                    $form.find('[name="payment[card][cvc]"]').attr('disabled','disabled');
                    $form.find('[name="payment[card][name]"]').attr('disabled','disabled');

            		// Submit Form
            		$form.trigger('submit');

            		return true;

            	}

            });

	    },

        /**
         * Rebuild Form on Fail
         */

        rebuild: function(){

            var $form = $('.payments-form');

            $form.find('[name="payment[card][number]"]').attr('disabled',null);
            $form.find('[name="payment[card][expiry]"]').attr('disabled',null);
            $form.find('[name="payment[card][cvc]"]').attr('disabled',null);
            $form.find('[name="payment[card][name]"]').attr('disabled',null);
            $form.find('[name="payment[card][name]"]').attr('disabled',null);
            $form.find('[name="payment[token]"]').remove();
            $form.find('[name="payment[gateway]"]').remove();

        },

        /**
         * Update Card via the payment gateway
         */

        updateCard: function( card, $form ){

            Stripe.card.createToken(card, function(status, response) {

                if(response.error){

                    // Error
                    console.log("ERROR: Stripe Token");
                    console.log("-- " + response.error.message);

                    // Display Error
                    Payments.form.throwError(response.error.message,$form);

                    return false;

                } else {

                    // Success
                    console.log("SUCCESS: Stripe Token");


                    // Append Token To Form
                    $form.append($('<input type="hidden" name="payment[token]">').val(response.id));
                    $form.append($('<input type="hidden" name="payment[gateway]">').val('stripe'));

                    // Disable Fields so they dont post
                    $form.find('[name="payment[card][number]"]').attr('disabled','disabled');
                    $form.find('[name="payment[card][expiry]"]').attr('disabled','disabled');
                    $form.find('[name="payment[card][cvc]"]').attr('disabled','disabled');
                    $form.find('[name="payment[card][name]"]').attr('disabled','disabled');

                    // Submit Form
                    $form.trigger('submit');

                    return true;

                }

            });

        }






	}

	// window.GatewayStripe = GatewayStripe;

	// GatewayStripe.init();
	Payments.gateway.stripe.init();

});