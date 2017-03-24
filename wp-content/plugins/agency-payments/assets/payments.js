/**
 * Agency Payments
 * ------------------------------------
 *
 *
 */

jQuery(document).ready(function($) {


	// Setup Global Payments Method
	var Payments = {};

	// Create Gateway Object
	Payments.gateway = {};



	/**
	 * THE PAYMENT FORM
	 *
	 */

	Payments.form = {

		frequency: 'single',
		amount: 0,
		gateway: false,
		actions: {},

	    init: function(){

	    	// Screens
	    	this.screens();

	    	// Frequency
	    	this.frequency();

	    	// Dollar Handles
	    	this.dollarHandles();

	    	// Validation
	    	this.validation();

	    	// Gateways
	    	this.gateways();

	    	// Saved Cards
	    	this.savedCards();

	    },

	    /**
	     * Screens
	     * The main screen based navigations setup
	     */

	    screens: function(){

	    	// Screen Controls

			$('a[data-payment-control]').click( function(event) {

				// Get Direction
				var forward = ($(this).data('paymentControl') === 'next');

				var $prev = $('[data-payment-control].active');

				if (forward) {

					// Validation
					if ($('[data-payment-dollar-handle].active').length <= 0 || Payments.form.amount <= 0) {

						$prev.find('.error-handles').text('Please select an amount').attr('role', 'alert');

						return;

					} else {

						$prev.find('.error-handles').text('').removeAttr('role');

					}

					if (($('[data-payment-control].active input, [data-payment-control].active select').length > 0) && !$('[data-payment-control].active input, [data-payment-control].active select').valid()) {

						$('[data-payment-control].active .error').filter(':first').focus();
						return;

					}
					
					$prev.each( function() {

						setStates( $(this), $(this).next() );

					});

					Payments.form.trigger('PAYMENTS_FORM_SCREEN_NEXT',this);

				} else {

					$prev.each( function() {

						setStates( $(this), $(this).prev() );

					});

					Payments.form.trigger('PAYMENTS_FORM_SCREEN_PREV',this);

				}

				$('a[data-payment-control*="e"]').hide();
				$('[data-payment-submit]').hide().attr('disabled','disabled');
		

				if ($('[data-payment-control].active').prev('[data-payment-control]').length > 0) $('a[data-payment-control="prev"]').show();
				if ($('[data-payment-control].active').next('[data-payment-control]').length > 0) $('a[data-payment-control="next"]').show();
				else $('[data-payment-submit]').show().removeAttr('disabled');

				// Set active states
				function setStates( prev, next ) {

					prev.removeClass('active');

					next.addClass('active');

					// Aria labels for screen reader
					if ( prev.parents('.navigation').length > 0 ) {

						prev.removeAttr('aria-label').attr('aria-hidden', 'true');

						next.removeAttr('aria-hidden').attr('aria-label', 'Step ' + next.text());

					}

					// Keyboard accessible focus
					next.find('*').filter(':input:visible:first').focus();

				}

			});

	    },

	    /**
	     * Frequency
	     * Setup frequency events and set the default frequnecy
	     * @return null
	     */

	    frequency: function() {

	    	if ($('[data-payment-dollar-handle]').length <= 0) return;

	    	// Setup Change Event
	    	$('[name="payment[frequency]"]').on('change', function(e){

	    		// Remove Active Class
	    		$('[data-payment-dollar-handle]').removeClass('frequency-active');
	    		
	    		// Add Active Class
	    		var freq = $(this).val();

	    		$('[data-payment-frequency="'+freq+'"').addClass('frequency-active');

	    		// Clear Selected Dollar Handle If Not Other
	    		$('[data-payment-dollar-handle].active').each( function() {

    				if ( $(this).attr('data-payment-frequency') == freq ) {
    					Payments.form.setAmount($(this).val());
    				}
    				
    			});

	    		Payments.form.trigger('PAYMENTS_FORM_FREQUENCY_CHANGE',this);

	    	});

	    	// Set Default Frequency
	    	// $('[name="payment[frequency]"]').trigger('change');

	    	// Set Initial Frequency
	    	$('[data-payment-frequency="'+$('[name="payment[frequency]"]').val()+'"').addClass('frequency-active');
	    	

	    },

		/**
		 * Dollar Handles
		 * The main screen based navigations setup
		 */

	    dollarHandles: function(){

	    	if($('[data-payment-dollar-handle]').length <= 0) return;

	    	// Set Initial Value
	    	Payments.form.setAmount($('[data-payment-dollar-handle].active').data('paymentDollarHandle'));

	    	// Value Click
	    	$('[data-payment-dollar-handle]').on('click',function(e){

	    		e.preventDefault();

	    		// Set Amount To Active
	    		$('[data-payment-dollar-handle]').removeClass('active');
	    		$(this).addClass('active');

	    		// Save Amount
	    		if($(this).data('paymentDollarHandle') != 'other'){
		    		Payments.form.setAmount($(this).data('paymentDollarHandle'));
		    		// Payments.form.setFrequency($(this).data('paymentFrequency'));
		    	} else {
		    		if($(this).val() <= 0 ) $(this).val(1);
		    		$(this).trigger('change');
		    	}

		    	Payments.form.trigger('PAYMENTS_FORM_DOLLAR_HANDLE_CLICK',this);

	    	});

	    	// Other Input
	    	$('input[data-payment-dollar-handle]').on('change keyup focus', function(e){

	    		console.log("CHANGE OTHER");
	    		// Set Amount To Active
	    		$('[data-payment-dollar-handle]').removeClass('active');
	    		$(this).addClass('active');

	    		// Save Amount
	    		Payments.form.setAmount($(this).val());

	    		Payments.form.trigger('PAYMENTS_FORM_OTHER_INPUT_CHANGE',this);

            });

	    },

	    /**
	     * Set Frequency
	     * Save and update the frequency
	     * @param string value the frequency to save
	     */

	    setFrequency: function( value ){

	    	// Payments.form.frequency = value;
	    	// $('input[name="payment[frequency]"]').val(value);

	    },

	    /**
	     * Set Amount
	     * Save and update the dollar amount
	     * @param number value the dollar amount to save
	     */

	    setAmount: function( value ){

	    	Payments.form.amount = parseFloat(value).toFixed(2);
	    	$('input[name="payment[amount]"]').val(Payments.form.amount);
	    	console.log("Set Amount: " + value);
	    	console.log(Payments.form.amount);

	    	Payments.form.trigger('PAYMENTS_FORM_SET_AMOUNT',value);

	    },

	    /**
	     * Get Current Screen
	     * Returns the index of the current screen
	     */

	    getCurrentScreen: function(){

	    	var currentScreen;
	    	if($('[data-screen-target].active').data('screenTarget').indexOf(1) != -1) currentScreen = 1;
	    	else if($('[data-screen-target].active').data('screenTarget').indexOf(2) != -1) currentScreen = 2;
	    	else if($('[data-screen-target].active').data('screenTarget').indexOf(3) != -1) currentScreen = 3;

	    	return currentScreen;


	    },

	    /**
	     * From Validation
	     * Validate form fields and submissions
	     */

	    validation: function(){

	    	$('[name="payment[card][number]"]').payment('formatCardNumber');
	    	$('[name="payment[card][expiry]"]').payment('formatCardExpiry');
	    	$('[name="payment[card][cvc]"]').payment('formatCardCVC');

	    	// Submission
	    	$('.payments-form').validate({

	    		submitHandler: function(form){

	    			var $form = $(form);

	    			// Submit The Form Via Ajax

				    $.ajax({
				        url : $form.attr("action"),
				        type: "POST",
				        data : $form.serializeArray(),
				        success:function(data, textStatus, jqXHR){

				            if(data.success == true){

				            	Payments.form.onSuccess(data);

				            } else {

				            	Payments.form.onFail(data);

				            }

				        },
				        error: function(jqXHR, textStatus, errorThrown){
				            Payments.form.onFail(errorThrown);
				        }

				    });

	    			// Force Error
	    			// var data = {};
	    			// data.success = false;
	    			// data.error = "This is a custom error from the gateway";
	    			// Payments.form.onFail(data);

				    return false;

	    		}

	    	});

	    },

	    /**
	     * Get Card
	     * Get the card from the form input and return as an object
	     * @return object
	     */
	    getCard: function(){

	    	var exp = $.payment.cardExpiryVal($('[name="payment[card][expiry]"]').val());
			var card = {
				number: $('[name="payment[card][number]"]').val(),
                cvc: $('[name="payment[card][cvc]"]').val(),
                name: $('[name="payment[card][name]"]').val(),
                exp_month: exp.month,
                exp_year: exp.year
			};

			return card;

	    },

	    /**
	     * Saved Cards
	     * Toggle Saved or New Cards for Logged in users
	     */

	    savedCards: function(){

	    	if($('.payments-saved-cards').length <= 0) return;

	    	$('input[name="payment[customer_id]"]').on('change',function(e){
	    		
	    		var $input = $(this);
	    		if($input.val() == 'newcard'){

	    			$('.js-payments-new-card').addClass('active');

	    		} else {

	    			$('.js-payments-new-card').removeClass('active');

	    		}

	    	});

	    },

	    /**
	     * Gateways
	     * Setup up events for the payment gateways
	     */

	    gateways: function(){

	    	// Set Gateway on Click
	    	$('[data-payment-gateway]').on('click',function(e){

	    		e.preventDefault();

	    		// Set The Gateway
    			Payments.form.gateway = $(this).data('paymentGateway');

    			// Process Form
    			Payments.form.process();



	    	});



	    },

	    /**
	     * Process
	     * Submit the payments form
	     */

	    process: function(){

	    	// Saved or New Card
	    	var cardType = 'saved';
	    	if($('.payments-saved-cards').length <= 0 || $('input[name="payment[customer_id]"]:checked').val() == 'newcard') cardType = 'new';

	    	// Process New Card
	    	var card = null;
	    	if(cardType == 'new'){

	    		// Validate Card Details
	    		if(($('[data-payment-control].active input, [data-payment-control].active select').length > 0) && !$('[data-payment-control].active input, [data-payment-control].active select').valid()) return;

	    		// Set to Processing
    			$('.payments-form-wrap').addClass('processing');

    			// Get The Credit Card
    			card = Payments.form.getCard();

    		} else {

    			// Validate Saved Card
    			if(!$('input[name="payment[customer_id]"]').valid()) return;

    			// Set to Processing
    			$('.payments-form-wrap').addClass('processing');

    			card = {};
    			card.savedCard = true;

    		}

    		// Process The Payments
    		Payments.gateway[Payments.form.gateway].process( card );

	    },

	    /**
	     * Throw Erros
	     * Global error handling for all payment gateways
	     */

	    throwError: function( error, $form ){

	    	$('.payments-form-wrap').removeClass('processing');

	    	$('.payments-form-wrap .errors').text(error);
	    	$('.payments-form-wrap .errors').addClass('active').focus();

	    	if (!$form) return;

	    	$form.find('.errors').text(error);
	    	$form.find('.errors').addClass('active').focus();

	    },

	    onSuccess: function( data ){

	    	console.log("SUCCESSFUL DONATION");
	    	console.log(data);

	    	Payments.form.trigger('PAYMENTS_FORM_ON_SUCCESS',data);

	    	if(data.message){
	    		$('.payments-form-wrap .js-success-message').html(data.message);
	    		$('.payments-form-wrap .js-success-message').addClass('active');
	    		$('.payments-form-wrap').addClass('success');

	    	} else if (data.redirect){
	    		window.location.href = data.redirect;
	    	}

	    },

	    onFail: function( data ){

	    	console.log("FAILED DONATION");
	    	console.log(data);

	    	Payments.form.trigger('PAYMENTS_FORM_ON_FAIL',data);

	    	$('.payments-form-wrap').removeClass('processing');
	    	$('.payments-form-wrap .errors').text(data.error);
	    	$('.payments-form-wrap .errors').addClass('active').focus();

	    	Payments.gateway[Payments.form.gateway].rebuild();

	    	//data.error

	    	// Back to Page One...
	    	// Display error
	    	// Fade navigation back in...
	    	// Read Add Credit Card Fields...


	    },

	    trigger: function( function_name, data ){

	    	if(!Payments.form.actions[function_name]) return;
	    	for( var i in Payments.form.actions[function_name]){

	    		Payments.form.actions[function_name][i](data);

	    	}
	    	

	    },

	    on: function( function_name, cb){

	    	if(!Payments.form.actions[function_name]) Payments.form.actions[function_name] = [];
	    	Payments.form.actions[function_name].push(cb);

	    }



	}

	/**
	 * The Member Account Edit Form
	 * This will only apply is the Agency Accounts plugin is activated
	 *
	 */

	Payments.manager = {

		init: function(){

			// Edit Subscription
			this.editSubscription();

			// Cancle Subscription
			this.cancelSubscription();

			// Edit Card
			this.editCard();

		},

		/**
		 * Edit Subscriptions
		 * Setup the events for editing subscriptions
		 * @return null
		 */

		editSubscription: function(){


			// Edit Subscription
			$('.js-edit-subscription').on('click',function(e){

				e.preventDefault();
				$('[data-account-subscription-cancel-form="'+$(this).data('subscriptionEditForm')+'"]').removeClass('active');
				$('[data-account-subscription-edit-form="'+$(this).data('subscriptionEditForm')+'"]').toggleClass('active');

			});

		},

		/**
		 * Cancel Subscriptions
		 * Setup the events for cancelling subscriptions
		 * @return null
		 */

		cancelSubscription: function(){

			$('.js-cancel-subscription').on('click',function(e){

				e.preventDefault();
				$('[data-account-subscription-edit-form="'+$(this).data('subscriptionCancelForm')+'"]').removeClass('active');
				$('[data-account-subscription-cancel-form="'+$(this).data('subscriptionCancelForm')+'"]').toggleClass('active');

			});
		},

		/**
		 * Edit Card Source
		 * Setup the events for editing card sources
		 * @return null
		 */

		editCard: function(){

			// Show Form
			$('.js-edit-card').on('click',function(e){

				e.preventDefault();
				$('[data-account-subscription-edit-card-form]').removeClass('active');
				$('[data-account-subscription-edit-card-form="'+$(this).data('subscriptionEditCardForm')+'"]').addClass('active');

			});

			// Gateway Save Card
			$('[data-payment-gateway-edit-card]').on('click',function(e){

				e.preventDefault();

	    		// Set The Gateway
    			var gateway = $(this).data('paymentGatewayEditCard');

    			// Get This Credit Card
    			var $editForm = $(this).parent();
    			var exp = $.payment.cardExpiryVal($editForm.find('[name="payment[card][expiry]"]').val());

				var card = {
					number: $editForm.find('[name="payment[card][number]"]').val(),
	                cvc: $editForm.find('[name="payment[card][cvc]"]').val(),
	                name: $editForm.find('[name="payment[card][name]"]').val(),
	                exp_month: exp.month,
	                exp_year: exp.year
				};

    			// Process The Payments
    			Payments.gateway[gateway].updateCard( card, $(this).closest('form') );

			});

		}





	}


	// ------------------------------------
	// GO
	// ------------------------------------

	// Export
	window.Payments = Payments;

	Payments.form.init();

	if($('.payments-account').length > 0) Payments.manager.init();

	// Action Example
	// Payments.form.on('PAYMENTS_FORM_SCREEN_NEXT',function(element){ alert("CALLED A DO ACTION"); })
	// Payments.form.on('PAYMENTS_FORM_SCREEN_NEXT',function(element){ alert("CALLED A 2ND DO ACTION"); })




});
