<?php

namespace Payments;

class Transaction {

	public $ID;

	public $name;

	public $date;

	public $user_id;

	public $transaction_id;

	public $customer_id;

	public $amount;

	public $currency;

	public $receipt_id;

	public $gateway;

	public $status = 'draft';

	public $subscription_id;

	public $meta;

	function __construct($obj = null){


		// Setup From A Payment
		if ($obj instanceof Payment){

			$this->name = $obj->transaction_id . ' ' . $obj->email;
			$this->user_id = $obj->user_id;
			$this->transaction_id = $obj->transaction_id;
			$this->customer_id = $obj->customer_id;
			$this->amount = $obj->amount;
			$this->currency = $obj->currency;
			$this->receipt_id = $obj->receipt_id;
			$this->gateway = $obj->gateway;
			$this->appeal_id = $obj->appeal_id;
			$this->fund = $obj->fund;
			$this->meta = $obj->meta;
			return;

		}

		// Set up from a saved Wordpress post
		if (is_numeric($obj)) $obj = get_post($obj);
		if ($obj->post_type != 'transaction') return false;
		if ( empty($obj) ) return false;

		// Set Up Fields
		$this->ID = $obj->ID;
		$this->name = $obj->post_title;
		$this->status = $obj->post_status;
		$this->date   = $obj->post_date;
		$this->user_id = get_post_meta($this->ID,'user_id',true);
		$this->transaction_id = get_post_meta($this->ID,'transaction_id',true);
		$this->customer_id = get_post_meta($this->ID,'customer_id',true);
		$this->amount = get_post_meta($this->ID,'amount',true);
		$this->currency = get_post_meta($this->ID,'currency',true);
		$this->receipt_id = get_post_meta($this->ID,'receipt_id',true);
		$this->gateway = get_post_meta($this->ID,'gateway',true);
		$this->appeal_id = get_post_meta($this->ID,'appeal_id',true);
		$this->fund = get_post_meta($this->ID,'fund',true);
		$this->meta = get_post_meta($this->ID,'meta',true);
		$this->subscription_id = get_post_meta($this->ID,'subscription_id',true);

		// Set up User details needed for export
		if(!$this->user_id) return;
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
			$new['post_type'] = 'transaction';

			// Create UserAction
			$this->ID = wp_insert_post($new);
			$this->status = 'publish';
			$this->date = get_the_date('Y-m-d',$this->ID);

			// Do Action
			do_action('payments_new_transaction_success',$this);

		}

		// Update Post
		$update = array();
		$update['ID'] = $this->ID;
		$update['post_title'] = $this->name;
		$update['post_status'] = $this->status;
		$update['post_type'] = 'transaction';

		$post_id = wp_update_post( $update, true );
		if (is_wp_error($post_id)) return false;

		// Update Meta
		update_post_meta($this->ID,'user_id',$this->user_id);
		update_post_meta($this->ID,'transaction_id',$this->transaction_id);
		update_post_meta($this->ID,'customer_id',$this->customer_id);
		update_post_meta($this->ID,'amount',$this->amount);
		update_post_meta($this->ID,'currency',$this->currency);
		update_post_meta($this->ID,'receipt_id',$this->receipt_id);
		update_post_meta($this->ID,'gateway',$this->gateway);
		update_post_meta($this->ID,'appeal_id',$this->appeal_id);
		update_post_meta($this->ID,'fund',$this->fund);
		update_post_meta($this->ID,'meta',$this->meta);
		update_post_meta($this->ID,'subscription_id',$this->subscription_id);

		// Do Action
		do_action('payments_update_transaction_success',$this);

		return true;

	}

	/**
	 * Export
	 * Convert the row into exportable fields
	 * @return array
	 */

	public function export(){

		$fields = $this;
		$fields = apply_filters('payments_format_transaction_export',$fields);

		$e = array();
		foreach($fields as $key=>$value){

			$e[$key] = $value;

		}
		return $e;


	}





}
?>