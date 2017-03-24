<?php

require(realpath(dirname(__FILE__)) . '/../../lib/stripe-php-3.4.0/init.php');

/**
 * Braintree wrapper class
 *
 * @class Donations_Braintree
 * @version 0.1
 */

class Donations_Stripe {

    public static $options = array();

    public static function initialize($options) {

        self::$options = $options;

        \Stripe\Stripe::setApiKey($options['stripe_secret']);

    }

    public static function errors($error) {

        $body = $error->getJsonBody();
        $err  = $body['error'];

        return array('success' => false, 'error' => $err['message']);

    }

    public static function add_customer($vars) {

        try {
            $result = \Stripe\Customer::create(array(
                "description" => $vars['email'],
                "email" => $vars['email'],
                "source" => $vars['stripe_token']
            ));
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

        return array('success' => true, 'response' => array(
            'customer_id' => $result->id,
            'original' => $result
        ));

    }

    public static function charge_customer($vars) {

        $receipt_id = time() . '_' . rand(100, 999);

        try {

            if ($vars['customer_id']) {

                $result = \Stripe\Charge::create(array(
                    'amount' => $vars['amount'] * 100,
                    'customer' => $vars['customer_id'],
                    'currency' => $vars['currency'],
                    'description' => $vars['description'] . ' - #' . $receipt_id,
                    'statement_descriptor' => get_option('donations_stripe_statement')
                ));

            } else {

                $result = \Stripe\Charge::create(array(
                    'amount' => $vars['amount'] * 100,
                    'source' => $vars['stripe_token'],
                    'currency' => $vars['currency'],
                    'metadata' => array( 'email' => $vars['email'] ),
                    'description' => $vars['description'] . ' - #' . $receipt_id,
                    'statement_descriptor' => get_option('donations_stripe_statement')
                ));

            }

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

        return array('success' => true, 'response' => array(
            'original' => $result,
            'gateway' => 'stripe',
            'transaction_id' => $result->id,
            'receipt_id' => $receipt_id
        ));

    }

    public static function get_customer($id) {

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

    public static function update_customer($id, $source) {


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
}
