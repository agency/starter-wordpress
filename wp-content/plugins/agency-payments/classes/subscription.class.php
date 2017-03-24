<?php

namespace Payments;

class Subscription {

	// The Wordpress ID
	public $ID;

	// The Wordpress Title
	public $name;

	// The Wordpress Post Date
	public $date;

	// The Wordpress Status
	public $status = 'draft';

	// The Wordpress User ID
	public $user_id;

	// The Dollar Amount
	public $amount;

	// The Date the subscription started
	public $start_date;

	// The date of the next charge
	public $next_renewal_date;

	// The date the payment lapsed
	public $lapsed_on;

	// The chosen payment gateway
	public $gateway;

	// The chosen currency
	public $currency;

	// The number of attemps the payment tried.
	public $failed_payments;

	// The Payment Gateway Customer ID
	public $customer_id; // Saved on the subscription incase there are more than one per user using different gateways

	// The Donation Appeal ID
	public $appeal_id;

	// The Donation Fund
	public $fund;

	// Custom Meta
	public $meta;


	/**
	 * Construct
	 * @param \Payments\Payment|INT|WP_Post|Null $obj
	 * @return null
	 */

	function __construct($obj = null){


		// Setup From A Payment into a New Subscription
		if ($obj instanceof Payment){

			$this->name = $obj->email; // The wordpress title will be the email address
			$this->amount = $obj->amount;
			$this->start_date = date('Ymd');
			$this->next_renewal_date = $this->update_renewal_date($this->start_date);
			$this->gateway = $obj->gateway;
			$this->currency = $obj->currency;
			$this->customer_id = $obj->customer_id;
			$this->user_id = $obj->user_id;
			$this->appeal_id = $obj->appeal_id;
			$this->fund = $obj->fund;
			$this->meta = $obj->meta;
			return;

		}

		// Set up from a saved Wordpress post
		if (is_numeric($obj)) $obj = get_post($obj);
		if ($obj->post_type != 'subscription') return false;
		if ( empty($obj) ) return false;

		// Set Up Fields From Wordpress
		$this->ID = $obj->ID;
		$this->name = $obj->post_title;
		$this->status = $obj->post_status;
		$this->date   = $obj->post_date;
		$this->user_id = get_post_meta($this->ID,'user_id',true);
		$this->amount = get_post_meta($this->ID,'amount',true);
		$this->currency = get_post_meta($this->ID,'currency',true);
		$this->gateway = get_post_meta($this->ID,'gateway',true);
		$this->appeal_id = get_post_meta($this->ID,'appeal_id',true);
		$this->customer_id = get_post_meta($this->ID,'customer_id',true);
		$this->start_date = get_post_meta($this->ID,'start_date',true);
		$this->next_renewal_date = get_post_meta($this->ID,'next_renewal_date',true);
		$this->lapsed_on = get_post_meta($this->ID,'lapsed_on',true);
		$this->failed_payments = get_post_meta($this->ID,'failed_payments',true);
		$this->fund = get_post_meta($this->ID,'fund',true);
		$this->meta = get_post_meta($this->ID,'meta',true);

	}

	/**
	 * Save
	 * Save this transaction as a new post in wordpress
	 * @return boolean
	 */

	public function save(){

		// Create A New Transaction if one doesn't exist
		if(empty($this->ID)){

			$new = array();
			$new['post_title'] = $this->name;
			$new['post_status'] = 'publish';
			$new['post_type'] = 'subscription';

			// Create UserAction
			$this->ID = wp_insert_post($new);
			$this->status = 'publish';
			$this->date = get_the_date('Y-m-d',$this->ID);

			// Do Action
			do_action('payments_new_subscription_success',$this);

		}

		// Update Post
		$update = array();
		$update['ID'] = $this->ID;
		$update['post_title'] = $this->name;
		$update['post_status'] = $this->status;
		$update['post_type'] = 'subscription';

		$post_id = wp_update_post( $update, true );
		if (is_wp_error($post_id)) return false;

		// Update Meta
		update_post_meta($this->ID,'user_id',$this->user_id);
		update_post_meta($this->ID,'amount',$this->amount);
		update_post_meta($this->ID,'currency',$this->currency);
		update_post_meta($this->ID,'gateway',$this->gateway);
		update_post_meta($this->ID,'appeal_id',$this->appeal_id);
		update_post_meta($this->ID,'customer_id',$this->customer_id);
		update_post_meta($this->ID,'start_date',$this->start_date);
		update_post_meta($this->ID,'next_renewal_date',$this->next_renewal_date);
		update_post_meta($this->ID,'lapsed_on',$this->lapsed_on);
		update_post_meta($this->ID,'failed_payments',$this->failed_payments);
		update_post_meta($this->ID,'fund',$this->fund);
		update_post_meta($this->ID,'meta',$this->meta);

		// Do Action
		do_action('payments_update_subscription_success',$this);

		return true;

	}

	/**
	 * Update Renewal Date
	 * @param date|null $date Ymd
	 * @return Date Ymd
	 */

	public function update_renewal_date( $date = null ){

		// Set the next date
		if(!$date) $date = date('Ymd');
		$this->next_renewal_date = date('Ymd', strtotime("+1 month", strtotime($date)));

		// Reset the failed date;
		$this->failed_payments = 0;

		return $this->next_renewal_date;

	}

	/**
	 * Increment Fail Counts
	 * Updates the number of fails
	 * @return null
	 */

	public function increment_fail_count(){

		$this->failed_payments += 1;

		if($this->failed_payments > 3) $this->lapsed_on = date('Ymd');

	}





}

?>