<?php
namespace Payments;

class Payment {

	public $amount;

	public $frequency;

	public $token;

	public $title;

	public $first_name;

	public $last_name;

	public $street;

	public $suburb;

	public $postcode;

	public $state;

	public $country;

	public $email;

	public $phone;

	public $appeal_id;

	public $gateway;

	public $customer_id;

	public $statement_descriptor;

	public $description;

	public $appeal_name;

	public $currency;

	public $transaction_id;

	public $receipt_id;

	public $user_id; // The Wordpress user id

	public $failure_reason;

	// Fund Code
	public $fund;

	// Custom Meta
	public $meta;


	function __construct($vars = null){

		if ($vars instanceof Subscription){

			if(!$vars->user_id) return;

			$this->amount = $vars->amount;
			$this->frequency = 'single'; // Because this is a single payment of a subscription
			$this->user_id = $vars->user_id;
			$this->appeal_id = $vars->appeal_id;
			$this->appeal_name = get_the_title($this->appeal_id);
			$this->gateway = $vars->gateway;
			$this->customer_id = $vars->customer_id;
			$this->description = $this->set_description('monthly');
			$this->currency = $vars->currency;
			$this->statement_descriptor = get_option('payments_statement_discriptor');
			$this->fund = $vars->fund;
			$this->meta = $vars->meta;

			if(!$this->currency) $this->currency = get_option('payments_gateway_stripe_currency');


			// User Info
			$this->get_user_details();

			// Return formatted
			return;

		} else if($vars instanceof Transaction){

			if(!$vars->user_id) return;

			$this->amount = $vars->amount;
			$this->frequency = 'single'; // Because this is a single payment of a subscription
			$this->user_id = $vars->user_id;
			$this->appeal_id = $vars->appeal_id;
			$this->appeal_name = get_the_title($this->appeal_id);
			$this->gateway = $vars->gateway;
			$this->customer_id = $vars->customer_id;
			$this->description = $this->set_description('monthly');
			$this->currency = $vars->currency;
			$this->statement_descriptor = get_option('payments_statement_discriptor');
			$this->transaction_id = $vars->transaction_id;
			$this->receipt_id = $vars->receipt_id;
			$this->fund = $vars->fund;
			$this->meta = $vars->meta;

			if(!$this->currency) $this->currency = get_option('payments_gateway_stripe_currency');


			// User Info
			$this->get_user_details();


			return;

		}


		// Set From Post Variables
		if($vars['amount']) $this->amount = $vars['amount'];
		if($vars['frequency']) $this->frequency = $vars['frequency'];
		if($vars['token']) $this->token = $vars['token'];
		if($vars['title']) $this->title = $vars['title'];
		if($vars['first_name']) $this->first_name = $vars['first_name'];
		if($vars['last_name']) $this->last_name = $vars['last_name'];
		if($vars['address']['street']) $this->street = $vars['address']['street'];
		if($vars['address']['suburb']) $this->suburb = $vars['address']['suburb'];
		if($vars['address']['postcode']) $this->postcode = $vars['address']['postcode'];
		if($vars['address']['state']) $this->state = $vars['address']['state'];
		if($vars['address']['country']) $this->country = $vars['address']['country'];
		if($vars['email']) $this->email = $vars['email'];
		if($vars['phone']) $this->phone = $vars['phone'];
		if($vars['id']) $this->appeal_id = $vars['id'];
		if($vars['gateway']) $this->gateway = $vars['gateway'];
		if($vars['customer_id']) $this->customer_id = $vars['customer_id'];
		if($vars['description']) $this->description = $vars['statement_description'];
		if($vars['currency']) $this->currency = $vars['currency'];
		if($vars['fund']) $this->fund = $vars['fund'];

		// Check for Saved Card
		if($this->customer_id == 'newcard') $this->customer_id = null;

		$this->appeal_name = get_the_title($this->appeal_id);

		$this->statement_descriptor = get_option('payments_statement_discriptor');


		if(!$this->description) $this->set_description($this->frequency);

		if(!$this->currency) $this->currency = get_option('payments_gateway_stripe_currency');

		// Override Freq IF we are using a toggle
		if($vars['toggle']['frequency'] == 'monthly') $this->frequency = 'monthly';

		// Set Custom Meta
		$this->meta = $vars['meta'];


	}

	public function set_description( $freq = 'single' ){

		$title = get_bloginfo('name');
		if($this->appeal_name) $title .= ' | ' . $this->appeal_name;

		$this->description = ($freq != 'single') ? 'Monthly Donation to ' . $title : 'Donation to ' . $title;
		return $this->description;

	}

	public function get_user_details(){

		$user = get_user_by('ID',$this->user_id);

		// Set User Details
		$this->email = $user->data->user_email;
		$this->first_name = get_user_meta($this->user_id,'first_name',true);
		$this->last_name = get_user_meta($this->user_id,'last_name',true);
		$this->phone = get_user_meta($this->user_id,'phone',true);
		$this->street = get_user_meta($this->user_id,'street_address',true);
		$this->suburb = get_user_meta($this->user_id,'suburb',true);
		$this->postcode = get_user_meta($this->user_id,'postcode',true);
		$this->state = get_user_meta($this->user_id,'state',true);
		$this->country = get_user_meta($this->user_id,'country',true);

	}

}

?>