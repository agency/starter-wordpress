<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name: Agency Payments
 * Depends: Agency Accounts
 * Description: A plugin for processing payments using Stripe
 * Author: Agency
 * Version: 1.0.1
 * Author URI: http://agency.sc/
 */


if (!defined('ABSPATH')) exit;

// Classes
require_once('classes/utils.class.php');
require_once('classes/payment.class.php');
require_once('classes/gateway.class.php');
require_once('classes/transaction.class.php');
require_once('classes/subscription.class.php');
require_once('classes/source.class.php');
require_once('classes/account-manager.class.php');

// Gateways
require_once('gateways/stripe/stripe.class.php');


// Registration Hooks
register_activation_hook( __FILE__, array( 'Agency_Payments', 'install' ) );
register_deactivation_hook(__FILE__, array( 'Agency_Payments', 'uninstall' ));

/**
 * Main Agency_Paymentser Class
 *
 * @class Agency_Payments
 * @version 0.1
 */

class Agency_Payments {

	public $errors = false;
	public $notices = false;
	public $slug = 'payments';
	public $gateways = array();

	function __construct() {

		$this->path = plugin_dir_path(__FILE__);
		$this->folder = basename($this->path);
		$this->dir = plugin_dir_url(__FILE__);
		$this->version = '1.0.3';

		$this->errors = array();
		$this->notices = array();

		// Plugin Setup Actions
		add_action('init', array($this, 'setup'), 10, 0);
		add_action('wp_enqueue_scripts', array($this, 'scripts'),11,1);
		add_action('admin_enqueue_scripts', array($this, 'scripts'));
		add_action('wp_loaded', array($this , 'forms'));
		add_action('parse_request', array($this , 'endpoints'));
		add_action('admin_menu', array($this, 'register_options_page'));

		// Notices (add these when you need to show the notice)
		add_action( 'admin_notices', array($this, 'admin_success'));
		add_action( 'admin_notices', array($this, 'admin_error'));

		// Template Actions
		add_action('payments_form_screen_one', function($appeal){ $this->template_include('form-screen-one.php',$appeal,'appeal'); });
		add_action('payments_form_screen_two', function($appeal){ $this->template_include('form-screen-two.php',$appeal,'appeal'); });
		add_action('payments_form_screen_three', function($appeal){ $this->template_include('form-screen-three.php',$appeal,'appeal'); });
		add_action('payments_form_screen_processing', function($appeal){ $this->template_include('form-screen-processing.php',$appeal,'appeal'); });

		// Default Payment Success Action
		add_action('payments_form_on_success',array($this,'payment_form_success'));

		// Scheduled Events
		add_action('payments_cron', array($this, 'cron'));

		// Shortcodes
		add_shortcode('payments_form', array($this,'shortcode_payment_form') );

		// Custom Meta Boxes
		add_action('add_meta_boxes', array($this,'add_metaboxes'));

		// Integration with Agency_Accounts: These will only apply if the Agency_Accounts plugin is enabled
		$this->account_manager = new \Payments\Account_Manager();

		// Enabled Gateways (later on lets make this a theme option with checkboxes)
		$this->gateways['stripe'] = new \Payments\Stripe();

		// Plugin Options
		$this->notifications_options = array(
			'payments_notifications_bcc',
			'payments_notifications_regularreceipts',
			'payments_notifications_receipt',
			'payments_notifications_receipt_subject',
			'payments_notifications_failed',
			'payments_notifications_failed_subject',
			'payments_notifications_expiry',
			'payments_notifications_expiry_subject',
			'payments_notifications_expirywarning',
			'payments_notifications_expirywarning_subject'
		);

		// Notification Replacments
		$this->notifications_replacements = array(
		     'email',
		     'transaction_id',
		     'receipt_id',
		     'description',
		     'amount',
		     'date',
		     'first_name',
		     'last_name',
		     'currency',
		     'street_address',
		     'postcode',
		     'country',
		     'failed_reason'
		);

		// Validate Required Options
		$this->validate_required_options();



	}

	/**
	 * Install
	 * Runs on the plugin activation hook
	 * @return null
	 */

	public static function install(){

		// Start Cron
		wp_schedule_event(time(), 'hourly', 'payments_cron');

	}

	/**
	 * Uninstall
	 * Runs on the plugin deactivation hook
	 * @return null
	 */

	public static function uninstall(){

		// Clear Cron
		wp_clear_scheduled_hook('payments_cron');
	}


	/**
	 * Setup
	 * Runs on init
	 * @return null
	 */

	public function setup() {

		// Create Custom Post Types
		$this->register_types();

		// Force Cron to Run
		if ($_GET['forceDonationProcess']) do_action('payments_cron');

		// Shortcake
		if (function_exists('shortcode_ui_register_for_shortcode')) {

			shortcode_ui_register_for_shortcode(
				'payments_form',
				array(
					'label' => 'Payment Form',
					'listItemImage' => 'dashicons-align-center',
					'post_type'     => array( 'post', 'page' ),
					'attrs' => array(

						array(
							'label' => 'Appeal',
							'attr'  => 'id',
							'type'  => 'post_select',
							'query' => array('post_type'=> 'appeal')
						)

					),
				)
			);
		}

	}


	/**
	 * Register Types
	 * @return null
	 */

	public function register_types() {

		// Create Appeals

		$args = array(
			'labels'             => \Payments\Utils::build_type_labels('Appeal', 'Appeals'),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'appeals' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_icon'			 => 'dashicons-chart-area',
			'menu_position'      => 40,
			'supports'           => array( 'title' )
		);

		register_post_type( 'appeal', $args );

		// Create Subscriptions

		$args = array(
			'labels'             => \Payments\Utils::build_type_labels('Subscription', 'Subscriptions'),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'subscriptions' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_icon'			 => 'dashicons-chart-area',
			'menu_position'      => 40,
			'supports'           => array( 'title' )
		);

		register_post_type( 'subscription', $args );

		// Create Transactions

		$args = array(
			'labels'             => \Payments\Utils::build_type_labels('Transaction', 'Transactions'),
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			// 'rewrite'            => array( 'slug' => 'transcation' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			// 'menu_icon'			 => 'dashicons-welcome-learn-more',
			'menu_position'      => 40,
			'supports'           => array( 'title' )
		);

		register_post_type( 'transaction', $args );

	}


	/**
	 * Process Form Payment
	 * Process a payment through the selected payment gateway from a form submission
	 * @param array $vars $_POST
	 * @return type
	 */

	public function process_form_payment( $vars ){

		// Format Data into A Payment
		$payment = new \Payments\Payment($vars['payment']);

		// Send to Processor
		$result = $this->process_payment($payment, true);

		if(!$result['success']) \Payments\Utils::output_json($result);

		// Create Subscription for Recurring Payments
		if($payment->frequency != 'single'){

			// Create Subscription
			$subscription = new \Payments\Subscription($payment);
			$subscription->save();

			// Update Transaction to Hold Subscription Reference
			$transaction = new \Payments\Transaction($result['response']['transaction_id']);
			$transaction->subscription_id = $subscription->ID;
			$transaction->save();


		}

		// Run Complete Actions: define custom ones using add_action('payments_on_successs',funcion(){});
		do_action('payments_form_on_success',$payment->appeal_id);

	}

	/**
	 * Process Payment
	 * Process a single payment through the chosen gateway
	 * @param \Payments\Payment $payment the payment object
	 * @return array the statues
	 */

	public function process_payment( $payment ){

		// Get Gateway
		$gateway = $this->get_gateway($payment);

		// Create Customer if we have to because this is a recurring donation
		// We need this to save the source
        if (!$payment->customer_id && $payment->frequency != 'single'){

        	$result = $gateway->add_customer($payment);
        	if (!$result['success']) return $result;

        	// Save Customer ID
        	$payment->customer_id = $result['response']['customer_id'];

        }

		// Charge The Customer
		$result = $gateway->charge_customer($payment);

		if(!$result['success']) return $result;

		// Success: Update the transaction and reciept numbers
		$payment->transaction_id = $result['response']['transaction_id'];
		$payment->receipt_id = $result['response']['receipt_id'];

		// Add The User to Wordpress
		if(!$payment->user_id) $payment->user_id = $this->add_wordpress_user($payment);

		// Add Transaction
		$transaction = new \Payments\Transaction($payment);
		$transaction->save();

		// Send Notifications
		$this->notifications($payment, $result);

		// Run Actions
		do_action('payments_on_success',$payment);

		// Return Successfully
		return array('success' => true, 'response' => array(
            'transaction_id' => $transaction->ID,
            'appeal_id' => $transaction->appeal_id,
            'payment' => $payment
        ));

	}

	/**
	 * Cron
	 * Run the scheduled events
	 * @return null
	 */

	public function cron() {

		// Get Subscriptions That need Running
		$subscriptions = $this->get_subscriptions_to_renew(15);

		if(!$subscriptions) return;

		// Process Subscriptions
		$this->process_subscriptions($subscriptions);

		// Check For Expiring Cards
		$this->validate_subscriptions_card_expiry($subscriptions);

	}

	/**
	 * Process Subscriptions
	 * Get the subscriptions that need to be renewed and process payments
	 * @param array $subscriptions array of \Payments\Subscription objects
	 * @return null
	 */

	public function process_subscriptions($subscriptions){

		// Convert Subcriptions into Payments
		foreach($subscriptions as $subscription){

			// Create A Payment From The Subscription Details
			$payment = New \Payments\Payment($subscription);

			// Process The Payment
			$result = $this->process_payment($payment);

			if(!$result['success']) {

				// Send Fail Notice
				$subscription->increment_fail_count();

				// Save Subscription
				$subscription->save();

			} else {

				// Update Next Billing Date
				$subscription->update_renewal_date();

				// Save Subscription
				$subscription->save();

				// Update Transaction with a subscription reference
				$transaction = new \Payments\Transaction($result['response']['transaction_id']);
				$transaction->subscription_id = $subscription->ID;
				$transaction->save();

			}

		}

	}

	/**
	 * Validate Subscriptions Card Expiry
	 * Check the customer credit card and send notifications if expiring soon
	 * @param array $subscriptions Array of \Payments\Subscription objects
	 * @return null
	 */

	public function validate_subscriptions_card_expiry($subscriptions){

		foreach($subscriptions as $subscription){

			// Get Gateway
			$gateway = $this->get_gateway($subscription);
			if($subscription->gateway != 'stripe') continue;

			// Get Customer Sources
			$sources = $gateway->get_customer_sources($subscription->customer_id);

			// New Payment: will help in mapping fields to emails. We won't change anything
			$payment = New \Payments\Payment($subscription);

			// Replacements
			$replacements = $this->build_notification_replacements($payment);
			$replacements['[date]'] = date('F j, Y'); // Override date to be now.

			// Validate Source
			foreach($sources as $source){

				if($source->expires_this_month()){

					// Send Expiry Warning Email
					\Payments\Utils::email($payment->email, get_option('payments_notifications_expirywarning_subject'), get_option('payments_notifications_expirywarning'), $replacements);

				} else if($source->is_expired()){

					// Send Expiried Email
					\Payments\Utils::email($payment->email, get_option('payments_notifications_expiry_subject'), get_option('payments_notifications_expiry'), $replacements);

				}

			}


		}

	}

	/**
	 * Get Subscriptions to Renew
	 * Returns an array of subscriptions objects that have a renewal date of less than or equal to today
	 * @return array Array of \Payments\Subscription objects
	 */

	public function get_subscriptions_to_renew($limit = -1){

		$args = array();
		$args['post_status'] = 'publish';
		$args['post_type'] = 'subscription';
		$args['posts_per_page'] = $limit;
		$args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key' => 'next_renewal_date',
				'value' => date('Ymd'),
				'type' => 'date',
				'compare' => '<='
			)
		);

		$posts = get_posts($args);
		$subscriptions = array();
		foreach($posts as $sub){

			$subscriptions[] = new \Payments\Subscription($sub);

		}

		return $subscriptions;

	}

	/**
	 * Get User Subscriptions
	 * @param int $user_id The wordpress user id
	 * @return array
	 */

	public function get_user_subscriptions($user_id){

		$args = array();
		$args['post_status'] = 'publish';
		$args['post_type'] = 'subscription';
		$args['posts_per_page'] = -1;
		$args['meta_key'] = 'user_id';
		$args['meta_value'] = $user_id;

		$posts = get_posts($args);
		if(!$posts) return;

		$subscriptions = array();

		foreach($posts as $sub){

			$subscriptions[] = new \Payments\Subscription($sub);

		}

		return $subscriptions;

	}



	/**
	 * Payment Success
	 * @param INT $appeal_id
	 * @return null
	 */

	public function payment_form_success($appeal_id){

		$appeal = get_post($appeal_id);
		$type = get_post_meta($appeal_id,'donation_complete_action_type',true);

		if($type == 'message'){

			\Payments\Utils::output_json(array('success' => true, 'message' => get_post_meta($appeal_id,'donation_success_message',true) ));

		} else {

			// \Payments\Utils::redirect(get_post_meta($appeal_id,'donation_success_redirect',true));
			\Payments\Utils::output_json(array('success' => true, 'redirect' => get_permalink(get_post_meta($appeal_id,'donation_success_redirect',true)) ));

		}

	}

	/**
	 * Get Gateway
	 * Get the gateway to use for a payment
	 * @param \Payments\Payment|\Payments\Subscription $payment
	 * @return Gateway
	 */

	public function get_gateway($payment){

		return $this->gateways[$payment->gateway];

	}

	/**
	 * Add Wordpress User
	 * @param \Payments\Payment $payment
	 * @return int The wordpress user id
	 */

	public function add_wordpress_user($payment){

		// Set Username
		$username = ($payment->customer_id) ? $payment->customer_id : $payment->email;

		// Check if this username exists
		$user_id = username_exists($username);

		// Check if email exists
		if(!$user_id) $user_id = email_exists($payment->email);

		if(!empty($user_id)) return $user_id;

		// Create users if still no id
		if(!$user_id) $user_id = wp_create_user($username, wp_generate_password(12), $payment->email);

		// Update User with payment details
		wp_update_user(array(
            'ID' => $user_id,
            'user_email' => $payment->email,
            'first_name' => $payment->first_name,
            'last_name' => $payment->last_name,
            'role' => 'member' // From Agency Accounts Plugin
        ));

        // Add Custom User Meta
        update_user_meta($user_id, 'street_address', $payment->street);
        update_user_meta($user_id, 'suburb', $payment->suburb);
        update_user_meta($user_id, 'country', $payment->country);
        update_user_meta($user_id, 'postcode', $payment->postcode);
        update_user_meta($user_id, 'state', $payment->state);
        update_user_meta($user_id, 'phone', $payment->phone);

        return $user_id;

	}

	/**
	 * Notifications
	 * Send email notifications to all defined users
	 * @param \Payments\Payment $payment
	 * @param array $result
	 * @return boolean
	 */

	public function notifications($payment, $result){

		// Send Emails Everywhere...
		if(!$payment->email) return;

		// Build Replacements List
		$replacements = $this->build_notification_replacements($payment);

        if($result['success']){

        	// Don't send emails on recurring payments from cron
        	if($payment->frequency != 'single' && !get_option('payments_notifications_regularreceipts')) return false;

        	// Get Appeal Specific Email
        	if($payment->appeal_id) $content = get_post_meta($payment->appeal_id, 'donation_receipt_email', true);

        	// Fallback Content
        	if(empty($content)) $content = get_option('payments_notifications_receipt');

        	// Send Email
        	\Payments\Utils::email($payment->email, get_option('payments_notifications_receipt_subject'), $content, $replacements);


        } else {

        	\Payments\Utils::email($payment->email, get_option('payments_notifications_failed_subject'), get_option('payments_notifications_failed'), $replacements);

        }





	}

	/**
	 * Build Notification replacements
	 * @param \Payments\Payment $payment
	 * @return array
	 */

	public function build_notification_replacements($payment){

		$replacements = array();
		foreach($this->notifications_replacements as $field){

			$key = $field;
			if($key == 'street_address') $key = 'street';
			$replacements['['.$field.']'] = $payment->{$key};

		}

		return $replacements;

	}

	/**
	 * Get Transactions
	 * Returns a list of transactions that were created between two dates
	 * @param date|null $from
	 * @param date|null $to
	 * @return array of \Payment\Transaction Objects
	 */

	public function get_transactions($from = null, $to = null){

		$args = array(
 			'post_type' => 'transaction',
 			'posts_per_page' => -1
 		);

 		if ($vars['payments_report_from'] || $vars['payments_report_to']) {
 			$args['date_query'] = array(array('inclusive' => true));
 		}

 		if ($from = $vars['payments_report_from']) {
 			$args['date_query'][0]['after'] = $from;
 		}

 		if ($before = $vars['payments_report_to']) {
 			$args['date_query'][0]['before'] = $before;
 		}

 		if ($filter = $vars['payments_report_filter']) {

 			if (!empty($filter)) {
 				$args['meta_query'] = array(
	 				array(
	 					'key' => 'transaction_type',
	 					'value' => $filter
	 				)
	 			);
	 		}

 		}

 		// Query Wordpress
 		$posts = get_posts($args);

 		// Build Transaction Array
 		$transactions = array();
 		foreach($posts as $p){

 			$transaction = new \Payments\Transaction($p);
 			$transactions[] = $transaction->export();
 		}

 		return $transactions;

	}

	/**
	 * Update Subscriptions
	 * Update a subscription object
	 * @param arra $vars $_POST variables
	 * @return null
	 */

	public function update_subscription($vars){

		// Get Current Subscription
		$subscription = new \Payments\Subscription($vars['subscription']['id']);

		// Update Value From Form
		$subscription->amount = $vars['subscription']['amount'];

		// Save
		$subscription->save();

		// Redirect
		\Payments\Utils::redirect($vars['_wp_http_referer'].'?msg=subscriptionupdatesuccess');


	}

	/**
	 * Cancel Subscription
	 * @param array $vars $_POST vars
	 * @return null
	 */

	public function cancel_subscription($vars){

		// Get Current Subscription
		$subscription = new \Payments\Subscription($vars['subscription']['id']);
		$subscription->status = 'draft';
		$subscription->save();

		// Redirect
		\Payments\Utils::redirect($vars['_wp_http_referer'].'?msg=subscriptionremovedsuccess');


	}

	/**
	 * Edit Source
	 * Update the gateway customer token
	 * @param array $vars $_POST variables
	 * @return null
	 */

	public function edit_source($vars){

		$gateway = $this->gateways[$vars['payment']['gateway']];

		// Get Customer ID
		$subscription = new \Payments\Subscription($vars['subscription']['id']);

		// Update the Customer Card
		$result = $gateway->update_customer($subscription->customer_id, $vars['payment']['token']);

		if(!$result['success']) \Payments\Utils::redirect($vars['_wp_http_referer'].'?msg=updatecardfailed');
		else \Payments\Utils::redirect($vars['_wp_http_referer'].'?msg=updatecardsucess');


	}

	/**
	 * Resend Receipt
	 * Send a receipt to the user
	 * @param int $transaction_id WP Post id of transaction
	 * @return null
	 */

	public function resend_receipt($transaction_id){

		// Get Transaction
		$transcation = new \Payments\Transaction($transaction_id);

		// Convert Transaction into a Payment
		$payment = new \Payments\Payment($transcation);

		// Resend the email
		$this->notifications($payment,array('success'=>true));

		return array('success' => true);

	}

	/**
	 * Scripts
	 * Include all required scripts into wordpress
	 * @return null
	 */

	public function scripts() {

		// Javascript
		wp_enqueue_script('jquery');
		wp_enqueue_script('stripe', 'https://js.stripe.com/v2/', '2.0', true);
		wp_enqueue_script('jquery.validate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js', array('jquery'), '1.2.3', true);
		wp_enqueue_script('jquery.payment', $this->dir . 'assets/jquery.payment.js', array('jquery', 'stripe'), '1.2.3', true);
		wp_enqueue_script('payments', $this->dir . 'assets/payments.js', array('jquery', 'stripe'), $this->version, true);

		// Styles
		wp_enqueue_style( 'payments', $this->dir . 'assets/payments.css', array(), $this->version);

		// Admin Scripts
		if (is_admin()) wp_enqueue_script('payments-admin', $this->dir . 'assets/admin.js', array('jquery'), $this->version, true);


	}


	/**
	 * Forms
	 * Process forms and run actions
	 * @return null
	 */

	public function forms() {

		if (!isset($_POST['payments_action'])) return;
		if(!wp_verify_nonce( $_POST['_wpnonce'], 'payments')){ \Payments\Utils::redirect($_POST['_wp_http_referer']); }

		switch ($_POST['payments_action']) {

			case 'process_payment':
				$result = $this->process_form_payment($_POST);
				break;

			case 'export':
				$results = $this->get_transactions($_POST['payments_report_from'], $_POST['payments_report_to']);
				\Payments\Utils::output_csv($results);
				break;

			case 'edit_subscription':
				$this->update_subscription($_POST);
				break;

			case 'cancel_subscription':
				$this->cancel_subscription($_POST);
				break;

			case 'edit_source':
				$this->edit_source($_POST);
				break;

			default:
				break;
		}

	}


	/**
	 * Endpoints
	 * Custom wordpress endpoints
	 * @param Object $wp
	 * @return null
	 */

	public function endpoints($wp) {

		$pagename = (isset($wp->query_vars['pagename'])) ? $wp->query_vars['pagename'] : $wp->request;

		switch ($pagename) {

			case 'payments/api/receipt/send':
				$results = $this->resend_receipt($_GET['tid']);
				\Payments\Utils::output_json($results);
				break;

			default:
				break;

		}

	}

	/**
	 * Register Plugin Options Pages
	 * @return null
	 */

	public function register_options_page() {

		// Custom Page
		add_menu_page('payments', 'Payments', 'manage_options', 'payments', function(){ $this->template_include('admin-reports.php'); }, 'dashicons-chart-area');

		// Customer Subpages
		add_submenu_page('payments', 'Appeals', 'Appeals', 'manage_options', 'edit.php?post_type=appeal');
		add_submenu_page('payments', 'Subscriptions', 'Subscriptions', 'manage_options', 'edit.php?post_type=subscription');
		add_submenu_page('payments', 'Transactions', 'Transactions', 'manage_options', 'edit.php?post_type=transaction');
		add_submenu_page('payments', 'Notifications', 'Notifications', 'manage_options', 'payment_emails', function(){ $this->template_include('admin-notifications.php'); });
		add_submenu_page('payments', 'Instructions', 'Instructions', 'manage_options', 'payment_instructions', function(){ $this->template_include('admin-instructions.php'); });
		add_submenu_page('payments', 'Settings', 'Settings', 'manage_options', 'payment_options', function(){ $this->template_include('options.php'); });

		// Register Options
		add_action('admin_init', array($this, 'plugin_options'));

	}

	/**
	 * Plugin Options
	 * @return null
	 */

	public function plugin_options() {

		// Global Options
		$options = array(
			'payments_default_gateway',
			'payments_statement_discriptor',
		);

		foreach ($options as $option) {
			register_setting('payments_options', $option);
		}

		// Notifications

		foreach ($this->notifications_options as $option) {
			register_setting('payments_notifications', $option);
		}

	}

	/**
	 * Add Metaboxes
	 * Add customer meta boxes to pages
	 * @return null
	 */

	public function add_metaboxes(){
		// add_meta_box('', 'Receipting', array($this,'metabox_receipting'), 'transaction', 'side', 'default');
		
		// Resend Recipts
		add_meta_box( 'payments-transaction-options', 'Transaction Options', function(){ $this->template_include('metabox-transaction-options.php'); },'transaction', 'side', 'default' );

		// Display Uneditable Meta
		add_meta_box( 'payments-transaction-meta', 'Transaction Meta Values', function(){ $this->template_include('metabox-transaction-meta.php'); },'transaction', 'normal', 'default' );
		add_meta_box( 'payments-subscription-meta', 'Subscription Meta Values', function(){ $this->template_include('metabox-subscription-meta.php'); },'subscription', 'normal', 'default' );


	}

	/**
	 * Template
	 * @param string $filename the name of the template file
	 * @return string the path to include
	 */

	public function template($filename) {

		// check theme
		$theme = get_template_directory() . '/'.$this->slug.'/' . $filename;

		if (file_exists($theme)) {
			$path = $theme;
		} else {
			$path = $this->path . 'templates/' . $filename;
		}
		return $path;

	}


   	/**
   	 * Template Incluce
   	 * @param string $template The name of the template file
   	 * @param *|null $data Data available within the file
   	 * @param string|null $name the value to call the date
   	 * @return null
   	 */

	public function template_include($template,$data = null,$name = null){


		if(isset($name)){ ${$name} = $data; }
		$path = $this->template($template);
		require($path);
	}

	/**
	 * Shorcode Payment Form
	 * Include the payments form as a shortcode
	 * @param array $attr
	 * @param string $content
	 * @return null
	 */

	public function shortcode_payment_form( $attr, $content ) {

		ob_start();
		$this->template_include('payments-form.php',$attr,'appeal');
		return ob_get_clean();

	}

	/**
	 * Outputs a WordPress error notice
	 *
	 * Push your error to $this->errors then show with:
	 * add_action( 'admin_notices', array($this, 'admin_error'));
	 */
	public function admin_error() {

		if(!$this->errors) return;

		foreach($this->errors as $error) :

	?>

		<div class="error settings-error notice is-dismissible">

			<p><strong><?php print $error ?></strong></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>

		</div>

	<?php

		endforeach;

	}

	/**
	 * Outputs a WordPress notice
	 *
	 * Push your error to $this->notices then show with:
	 * add_action( 'admin_notices', array($this, 'admin_success'));
	 */
	public function admin_success() {

		if(!$this->notices) return;

		foreach($this->notices as $notice) :

	?>

		<div class="updated settings-error notice is-dismissible">

			<p><strong><?php print $notice ?></strong></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>

		</div>

	<?php

		endforeach;

	}

	/**
	 * Validate Required Options
	 * @return array of errors
	 */

	public function validate_required_options(){

		$fail = false;
		foreach($this->notifications_options as $option){

			if(empty(get_option($option)) && $option != 'payments_notifications_regularreceipts') $fail = true;

		}

		if($fail == true){
			$this->errors[] = "Complete your Payment Setup <a href=\"/wp-admin/admin.php?page=payment_emails\">here</a>.";
		}


	}

}


// Include Template Tags
require_once('payments-template-tags.php');

// Call the plugin
$agency_payments = new Agency_Payments();