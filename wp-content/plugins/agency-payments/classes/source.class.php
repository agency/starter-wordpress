<?php

namespace Payments;

class Source {

	// The Gateway Source ID
	public $id;

	// The Gateway Object
	public $object;

	// The Last Four Digits
	public $last4;

	// The Expiry Month
	public $exp_month;

	// The Expiry Year
	public $exp_year;

	// Name
	public $name;

	function __construct(){}

	/**
	 * Expires This Month
	 * Validate if the source expires this month
	 * @return boolean
	 */

	public function expires_this_month(){

		if (date('Y-m', strtotime($this->exp_year . '-' . $this->exp_month)) == date('Y-m', time())) return true;
		return false;

	}

	/**
	 * Is Expired
	 * Validate if a source is expired
	 * @return boolean
	 */

	public function is_expired(){

		if(date('Y-m', strtotime($source->exp_year . '-' . $source->exp_month)) < date('Y-m')) return true;
		return false;

	}


}
?>