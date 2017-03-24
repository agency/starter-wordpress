<?php

namespace Payments;

class Account_Manager {

    function __construct(){

    	$this->path = str_replace('classes/', '', plugin_dir_path(__FILE__));
		$this->url = str_replace('classes/', '', plugin_dir_url(__FILE__));

		// Add To Navigation
		add_filter('accounts_sections', function( $sections ){ $sections[] = array('id' => 'subscriptions', 'href' => '/account/subscriptions', 'text' => 'Subscriptions'); return $sections; });

		// Add Subscription List
    	add_action('accounts_the_content_subscriptions',function(){ Utils::template_include($this->path.'/templates/account-subscriptions.php'); });

    	// Update Card Details
    	add_action('accounts_the_content_subscriptions',function(){ Utils::template_include($this->path.'/templates/account-cards.php'); });

        // Custom Messages
        add_filter('accounts_message_list',array($this,'message_list'));

    }

    /**
     * Message List Filter
     * @param array $message_list
     * @return array
     */

    public function message_list($message_list){

        $message_list['subscriptionupdatesuccess']['status'] = 'good';
        $message_list['subscriptionupdatesuccess']['message'] = "Thanks! We've successfully updated your subscription.";

        $message_list['subscriptionremovedsuccess']['status'] = 'good';
        $message_list['subscriptionremovedsuccess']['message'] = "Your subscription has be remove.";

        $message_list['updatecardfailed']['status'] = 'bad';
        $message_list['updatecardfailed']['message'] = "We're sorry, your card updated failed.";

        $message_list['updatecardsucess']['status'] = 'good';
        $message_list['updatecardsucess']['message'] = "Thanks! We've updated your card.";


        return $message_list;
    }

}

?>