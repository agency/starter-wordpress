<div class="stuffbox">

    <div class="inside">

        <h3><?php _e('Stripe Settings'); ?></h3>

        <hr>

        <div class="option">

            <p><b><label for="payments_gateway_stripe_mode">Mode</label></b><br><em>Live mode will except payments and charge your card. Test mode is for testing.</em></p>

            <p>

            	<select id="payments_gateway_stripe_mode" name="payments_gateway_stripe_mode">

            		<option value="live" <?php if( get_option('payments_gateway_stripe_mode') == 'live' ){ echo "selected"; } ?>>Live Mode</option>

            		<option value="test" <?php if( get_option('payments_gateway_stripe_mode') == 'test' ){ echo "selected"; } ?>>Test Mode</option>

             	</select>

            </p>

        </div>

        <hr>

        <div class="option">

            <p><b><label for="payments_gateway_stripe_currency">Currency</label></b></p>

            <p><?php global $agency_payments; ?>

                <select id="payments_gateway_stripe_currency" name="payments_gateway_stripe_currency">

                    <option>Please Select A Default Currency</option>

                    <?php foreach( $agency_payments->gateways['stripe']->currencies as $key=>$currency) : ?>

                        <option value="<?php echo $key; ?>" <?php if( get_option('payments_gateway_stripe_currency') == $key ){ echo "selected"; } ?>><?php echo $currency;?></option>

                    <?php endforeach; ?>


                </select>

             </p>

        </div>

        <hr>

        <h3>Stripe Keys</h3>

        <div class="option">
            <p><b><label for="payments_gateway_stripe_live_public_key">Live Public Key</label></b></p>
            <p><p><input type="text" class="regular-text" id="payments_gateway_stripe_live_public_key" name="payments_gateway_stripe_live_public_key" value="<?php echo esc_attr( get_option('payments_gateway_stripe_live_public_key') ); ?>"></p>
        </div>

        <div class="option">
            <p><b><label for="payments_gateway_stripe_live_secret_key">Live Secret Key</label></b></p>
            <p><p><input type="text" class="regular-text" id="payments_gateway_stripe_live_secret_key" name="payments_gateway_stripe_live_secret_key" value="<?php echo esc_attr( get_option('payments_gateway_stripe_live_secret_key') ); ?>"></p>
        </div>

        <div class="option">
            <p><b><label for="payments_gateway_stripe_test_public_key">Test Public Key</label></b></p>
            <p><p><input type="text" class="regular-text" id="payments_gateway_stripe_test_public_key" name="payments_gateway_stripe_test_public_key" value="<?php echo esc_attr( get_option('payments_gateway_stripe_test_public_key') ); ?>"></p>
        </div>

        <div class="option">
            <p><b><label for="payments_gateway_stripe_test_secret_key">Test Secret Key</label></b></p>
            <p><p><input type="text" class="regular-text" id="payments_gateway_stripe_test_secret_key" name="payments_gateway_stripe_test_secret_key" value="<?php echo esc_attr( get_option('payments_gateway_stripe_test_secret_key') ); ?>"></p>
        </div>

    </div>

</div>