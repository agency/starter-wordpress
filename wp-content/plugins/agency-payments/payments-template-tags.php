<?php

/**
 * Payments Template Include
 * @param string $template the filename of the tempalte
 * @param *|null $data data to make aavailable in the template file
 * @param string|null $name variable name for the data
 * @return null
 */

function payments_template($template, $data = null, $name = null){

	global $agency_payments;

	if(isset($name)){ ${$name} = $data; }
	$path = $agency_payments->template($template);
	include($path);

}

function get_notification_replacements(){

	global $agency_payments;
	return $agency_payments->notifications_replacements;

}

$USER_SUBSCRIPTIONS;
function get_user_subscriptions(){

	global $agency_payments;
	global $USER_SUBSCRIPTIONS;

	if($USER_SUBSCRIPTIONS) return $USER_SUBSCRIPTIONS;

	$USER_SUBSCRIPTIONS = $agency_payments->get_user_subscriptions(get_current_user_id());
	return $USER_SUBSCRIPTIONS;

}


function get_gateway_sources($subscription){

	global $agency_payments;
	$gateway = $agency_payments->get_gateway($subscription);
	$sources = $gateway->get_customer_sources($subscription->customer_id);
	return $sources;

}

function get_sources(){

	$subscriptions = get_user_subscriptions();
	if(!$subscriptions) return;

	$cards = array();

	foreach($subscriptions as $subscription){

		if($subscription->gateway != 'stripe') continue;
		$sources = get_gateway_sources($subscription);
		
		foreach($sources as $source){

			if($cards[$source->id]) continue;
			$cards[$source->id] = array();
			$cards[$source->id]['source'] = $source;
			$cards[$source->id]['customer_id'] = $subscription->customer_id;

		}

	}

	return $cards;

}


function the_payment_card_inputs( $source = null ){

	global $agency_payments;
	$agency_payments->template_include('form-credit-card-fields.php',$source,'source');



}

function get_payments_frequency($appeal_id){

	global $post;
	if(!$appeal_id) $appeal_id = $post->ID;

	$amounts = get_field('donation_amounts',$appeal_id);
	$freqs = array();
	foreach($amounts as $amount){
		$freqs[$amount['frequency']] = $amount['frequency'];
		$freq = $amount['frequency'];
	}

	if(count($freqs) < 2)
		return $freq;
	else
		return 'both';
}

?>