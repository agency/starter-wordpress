<?php

namespace Payments;

class Stripe extends Gateway {

	/**
	 * Construct
	 * Setup the payment gateway will all of its default funtionality
	 * @return null
	 */

	function __construct(){

		// Setup
		$this->name = 'Stripe';
		$this->path = str_replace('classes/', '', plugin_dir_path(__FILE__));
		$this->url = str_replace('classes/', '', plugin_dir_url(__FILE__));
		$this->currencies = $this->define_currencies();
		$this->version = '1.0.1';

        // Include Dependencies
        $this->include_dependencies();

		// Add Actions
		add_action('wp_enqueue_scripts', array($this, 'scripts'),10,1);
		add_action('admin_init', array($this, 'plugin_options'));
		add_action('payment_custom_options',function(){ Utils::template_include($this->path.'templates/options.php'); });
		add_action('wp_footer', array($this, 'footer'));
        add_action('payments_instructions_gateways', function(){ Utils::template_include($this->path.'templates/instructions.php'); });

		// Set Keys
		$this->keys = $this->set_keys();


        // Set Currency
        $this->currency = get_option('payments_gateway_stripe_currency') ? get_option('payments_gateway_stripe_currency') : 'AUD';


	}

    /**
     * Include Dependencies
     * @return null
     */

    public function include_dependencies(){

        require('stripe-php-3.4.0/init.php');
    }

	/**
	 * Scripts
	 * Include requires scripts specific to this payment gateway
	 * @return null
	 */

	public function scripts(){

        wp_enqueue_script('stripe', 'https://js.stripe.com/v2/', '2.0', true);
		wp_enqueue_script('gatewaystripe', $this->url.'assets/stripe.js', array('payments'), $this->version, true);

	}

	/**
	 * Plugin Options
	 * Register custom payment options specific to this gateway
	 * @return null
	 */

	public function plugin_options() {

		$options = array(
			'payments_gateway_stripe_mode',
			'payments_gateway_stripe_live_public_key',
			'payments_gateway_stripe_live_secret_key',
			'payments_gateway_stripe_test_public_key',
			'payments_gateway_stripe_test_secret_key',
            'payments_gateway_stripe_currency'
		);

		foreach ($options as $option) {
			register_setting('payments_options', $option);
		}

	}

	/**
	 * Get keys
	 * Retrieve the tokens for this gateway
	 * @return type
	 */

	public function set_keys(){

		$mode = trim(get_option('payments_gateway_stripe_mode'));

        $keys = array();
        $keys['live'] = array();
        $keys['live']['public'] = trim(get_option('payments_gateway_stripe_live_public_key'));
        $keys['live']['secret'] = trim(get_option('payments_gateway_stripe_live_secret_key'));
        $keys['test'] = array();
        $keys['test']['public'] = trim(get_option('payments_gateway_stripe_test_public_key'));
        $keys['test']['secret'] = trim(get_option('payments_gateway_stripe_test_secret_key'));

        // Setup Stripe
        \Stripe\Stripe::setApiKey($keys[$mode]['secret']);

        // Return Keys
        return $keys[$mode];

	}

	/**
	 * Define Available currencies
	 * Hardcodes list of available gateway currencies
	 * @return array
	 */

	public function define_currencies(){

        $currency = array();
        $currency['AED'] = 'United Arab Emirates Dirham';
        $currency['ALL'] = 'Albanian Lek';
        $currency['ANG'] = 'Netherlands Antillean Gulden';
        $currency['ARS'] = 'Argentine Peso';
        $currency['AUD'] = 'Australian Dollar';
        $currency['AWG'] = 'Aruban Florin';
        $currency['BBD'] = 'Barbadian Dollar';
        $currency['BDT'] = 'Bangladeshi Taka';
        $currency['BIF'] = 'Burundian Franc';
        $currency['BMD'] = 'Bermudian Dollar';
        $currency['BND'] = 'Brunei Dollar';
        $currency['BOB'] = 'Bolivian Boliviano';
        $currency['BRL'] = 'Brazilian Real';
        $currency['BSD'] = 'Bahamian Dollar';
        $currency['BWP'] = 'Botswana Pula';
        $currency['BZD'] = 'Belize Dollar';
        $currency['CAD'] = 'Canadian Dollar';
        $currency['CHF'] = 'Swiss Franc';
        $currency['CLP'] = 'Chilean Peso';
        $currency['CNY'] = 'Chinese Renminbi Yuan';
        $currency['COP'] = 'Colombian Peso';
        $currency['CRC'] = 'Costa Rican Colón';
        $currency['CVE'] = 'Cape Verdean Escudo';
        $currency['CZK'] = 'Czech Koruna';
        $currency['DJF'] = 'Djiboutian Franc';
        $currency['DKK'] = 'Danish Krone';
        $currency['DOP'] = 'Dominican Peso';
        $currency['DZD'] = 'Algerian Dinar';
        $currency['EGP'] = 'Egyptian Pound';
        $currency['ETB'] = 'Ethiopian Birr';
        $currency['EUR'] = 'Euro';
        $currency['FJD'] = 'Fijian Dollar';
        $currency['FKP'] = 'Falkland Islands Pound';
        $currency['GBP'] = 'British Pound';
        $currency['GIP'] = 'Gibraltar Pound';
        $currency['GMD'] = 'Gambian Dalasi';
        $currency['GNF'] = 'Guinean Franc';
        $currency['GTQ'] = 'Guatemalan Quetzal';
        $currency['GYD'] = 'Guyanese Dollar';
        $currency['HKD'] = 'Hong Kong Dollar';
        $currency['HNL'] = 'Honduran Lempira';
        $currency['HRK'] = 'Croatian Kuna';
        $currency['HTG'] = 'Haitian Gourde';
        $currency['HUF'] = 'Hungarian Forint';
        $currency['IDR'] = 'Indonesian Rupiah';
        $currency['ILS'] = 'Israeli New Sheqel';
        $currency['INR'] = 'Indian Rupee';
        $currency['ISK'] = 'Icelandic Króna';
        $currency['JMD'] = 'Jamaican Dollar';
        $currency['JPY'] = 'Japanese Yen';
        $currency['KES'] = 'Kenyan Shilling';
        $currency['KHR'] = 'Cambodian Riel';
        $currency['KMF'] = 'Comorian Franc';
        $currency['KRW'] = 'South Korean Won';
        $currency['KYD'] = 'Cayman Islands Dollar';
        $currency['KZT'] = 'Kazakhstani Tenge';
        $currency['LAK'] = 'Lao Kip';
        $currency['LBP'] = 'Lebanese Pound';
        $currency['LKR'] = 'Sri Lankan Rupee';
        $currency['LRD'] = 'Liberian Dollar';
        $currency['MAD'] = 'Moroccan Dirham';
        $currency['MDL'] = 'Moldovan Leu';
        $currency['MNT'] = 'Mongolian Tögrög';
        $currency['MOP'] = 'Macanese Pataca';
        $currency['MRO'] = 'Mauritanian Ouguiya';
        $currency['MUR'] = 'Mauritian Rupee';
        $currency['MVR'] = 'Maldivian Rufiyaa';
        $currency['MWK'] = 'Malawian Kwacha';
        $currency['MXN'] = 'Mexican Peso';
        $currency['MYR'] = 'Malaysian Ringgit';
        $currency['NAD'] = 'Namibian Dollar';
        $currency['NGN'] = 'Nigerian Naira';
        $currency['NIO'] = 'Nicaraguan Córdoba';
        $currency['NOK'] = 'Norwegian Krone';
        $currency['NPR'] = 'Nepalese Rupee';
        $currency['NZD'] = 'New Zealand Dollar';
        $currency['PAB'] = 'Panamanian Balboa';
        $currency['PEN'] = 'Peruvian Nuevo Sol';
        $currency['PGK'] = 'Papua New Guinean Kina';
        $currency['PHP'] = 'Philippine Peso';
        $currency['PKR'] = 'Pakistani Rupee';
        $currency['PLN'] = 'Polish Złoty';
        $currency['PYG'] = 'Paraguayan Guaraní';
        $currency['QAR'] = 'Qatari Riyal';
        $currency['RUB'] = 'Russian Ruble';
        $currency['SAR'] = 'Saudi Riyal';
        $currency['SBD'] = 'Solomon Islands Dollar';
        $currency['SCR'] = 'Seychellois Rupee';
        $currency['SEK'] = 'Swedish Krona';
        $currency['SGD'] = 'Singapore Dollar';
        $currency['SHP'] = 'Saint Helenian Pound';
        $currency['SLL'] = 'Sierra Leonean Leone';
        $currency['SOS'] = 'Somali Shilling';
        $currency['STD'] = 'São Tomé and Príncipe Dobra';
        $currency['SVC'] = 'Salvadoran Colón';
        $currency['SZL'] = 'Swazi Lilangeni';
        $currency['THB'] = 'Thai Baht';
        $currency['TOP'] = 'Tongan Paʻanga';
        $currency['TTD'] = 'Trinidad and Tobago Dollar';
        $currency['TWD'] = 'New Taiwan Dollar';
        $currency['TZS'] = 'Tanzanian Shilling';
        $currency['UAH'] = 'Ukrainian Hryvnia';
        $currency['UGX'] = 'Ugandan Shilling';
        $currency['USD'] = 'United States Dollar';
        $currency['UYU'] = 'Uruguayan Peso';
        $currency['UZS'] = 'Uzbekistani Som';
        $currency['VND'] = 'Vietnamese Đồng';
        $currency['VUV'] = 'Vanuatu Vatu';
        $currency['WST'] = 'Samoan Tala';
        $currency['XAF'] = 'Central African Cfa Franc';
        $currency['XOF'] = 'West African Cfa Franc';
        $currency['XPF'] = 'Cfp Franc';
        $currency['YER'] = 'Yemeni Rial';
        $currency['ZAR'] = 'South African Rand';
        return $currency;
    }

    /**
	 * Footer
	 * Include stripe publishable key
	 * @return string
	 */

	public function footer() {

		echo "<script>Stripe.setPublishableKey('{$this->keys['public']}');</script>";

	}

    /**
     * Errors
     * Parse errors specific to this gateway and return an array
     * @param object $error
     * @return array
     */

    public function errors($error) {

        $body = $error->getJsonBody();
        $err  = $body['error'];

        return array('success' => false, 'error' => $err['message']);

    }

// ------------------------------------
//
// INTEGRATIONS
//
// ------------------------------------


    /**
     * Charge Customer
     * Integrate with the gateway to charge a payment
     * @param \Payments\Payment $payment
     * @return array the result of the call
     */

    public function charge_customer( $payment ){

        // Create a unique receipt id
        $receipt_id = time() . '_' . rand(100, 999);

        // Try to charge
        try {

            if ($payment->customer_id) {

                $result = \Stripe\Charge::create(array(
                    'amount' => $payment->amount * 100,
                    'customer' => $payment->customer_id,
                    'currency' => ($payment->currency) ? $payment->currency : $this->currency,
                    'description' => $payment->description . ' - #' . $receipt_id,
                    'statement_descriptor' => $payment->statement_descriptor
                ));

            } else {

                $result = \Stripe\Charge::create(array(
                    'amount' => $payment->amount * 100,
                    'source' => $payment->token,
                    'currency' => ($payment->currency) ? $payment->currency : $this->currency,
                    'metadata' => array( 'email' => $payment->email ),
                    'description' => $payment->description . ' - #' . $receipt_id,
                    'statement_descriptor' => $payment->statement_descriptor
                ));

            }

        } catch(\Stripe\Error\Card $e) {
            return $this->errors($e);
        } catch(\Stripe\Error\InvalidRequest $e) {
            return $this->errors($e);
        } catch(\Stripe\Error\Authentication $e) {
            return $this->errors($e);
        } catch(\Stripe\Error\ApiConnection $e) {
            return $this->errors($e);
        } catch(\Stripe\Error\Base $e) {
            return $this->errors($e);
        } catch(Exception $e) {
            return $this->errors($e);
        }

        // Do Action
        do_action('payments_stripe_charge_success',$result);

        // Return
        return array('success' => true, 'response' => array(
            'original' => $result,
            'gateway' => 'stripe',
            'transaction_id' => $result->id,
            'receipt_id' => $receipt_id
        ));

    }

    /**
     * Add Customer
     * Integrate with the gateway to add a new customer
     * @param \Payments\Payment $payment
     * @return array status of the call
     */

    public function add_customer($payment) {


        try {

            $result = \Stripe\Customer::create(array(
                "description" => $payment->email,
                "email" => $payment->email,
                "source" => $payment->token
            ));

        } catch(\Stripe\Error\Card $e) {
            return $this->errors($e);
        } catch(\Stripe\Error\InvalidRequest $e) {
            return $this->errors($e);
        } catch(\Stripe\Error\Authentication $e) {
            return $this->errors($e);
        } catch(\Stripe\Error\ApiConnection $e) {
            return $this->errors($e);
        } catch(\Stripe\Error\Base $e) {
            return $this->errors($e);
        } catch(Exception $e) {
            return $this->errors($e);
        }

        // Do Action
        do_action('payments_stripe_add_customer_success',$result);

        return array('success' => true, 'response' => array(
            'customer_id' => $result->id,
            'original' => $result
        ));

    }

    /**
     * Get Customer
     * @param string $id the gateway customer_id
     * @return array
     */

    public function get_customer($id) {

        try {

            $result = \Stripe\Customer::retrieve($id);

        } catch(\Stripe\Error\Card $e) {
            return self::errors($e);
        } catch(\Stripe\Error\InvalidRequest $e) {
            return self::errors($e);
        } catch(\Stripe\Error\Authentication $e) {
            return self::errors($e);
        } catch(\Stripe\Error\ApiConnection $e) {
            return self::errors($e);
        } catch(\Stripe\Error\Base $e) {
            return self::errors($e);
        } catch(Exception $e) {
            return self::errors($e);
        }


        return $result;

    }


    public function update_customer($id, $source) {

        $customer = self::get_customer($id);

        if ($customer->sources->data) {

            $customer->source = $source;

            $result = $customer->save();

            if ($result->sources->data) {
                return array(
                    'success' => true
                );
            }

        }
        else  {
            return array(
                'error' => 'Could not retrieve customer'
            );
        }
    }

    /**
     * Get Customer Sources
     * @param int $id The gateway customer id
     * @return array
     */

    public function get_customer_sources($id){

        $customer = $this->get_customer($id);
        if(!$customer->sources->data) return false;

        $sources = array();

        // Create Sources
        foreach($customer->sources->data as $data){

            $source = new Source();
            $source->id = $data->id;
            $source->object = $data->object;
            $source->last4 = $data->last4;
            $source->exp_month = $data->exp_month;
            $source->exp_year = $data->exp_year;
            $source->name = $data->name;

            $sources[] = $source;

        }

        return $sources;


    }


}

?>