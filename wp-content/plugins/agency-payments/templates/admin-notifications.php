<?php
/**
 * Plugin Options Page
 *
 */

 // $fields = array(
 //     'email',
 //     'transaction_id',
 //     'receipt_id',
 //     'description',
 //     'amount',
 //     'date',
 //     'first_name',
 //     'last_name',
 //     'currency',
 //     'street_address',
 //     'postcode',
 //     'country',
 //     'failed_reason'
 // );
$fields = get_notification_replacements();
 ?>
<div class="wrap">

    <h2><?php _e('Payment Notifications'); ?></h2>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

			<!-- main content -->
			<div id="post-body-content" class="edit-form-section">

				<form method="post" action="options.php">

					<?php settings_fields('payments_notifications'); ?>
	    			<?php do_settings_sections('payments_notifications'); ?>

                    <div class="stuffbox">
                        <h3>Email Settings</h3>
                        <div class="inside">

                            <p>
                                <label for="payments_notifications_bcc">Send a copy of all emails to this address:</label><br>
                                <input type="email" name="payments_notifications_bcc" id="payments_notifications_bcc" value="<?php echo get_option('payments_notifications_bcc'); ?>">
                            </p>

                            <p>
                                <label for="payments_notifications_regularreceipts"><input type="checkbox" name="payments_notifications_regularreceipts" id="payments_notifications_regularreceipts" <?php if (get_option('payments_notifications_regularreceipts')) echo 'checked'; ?>> Send receipts for recurring donations</label>
                            </p>

                        </div>
                    </div>

					<div class="stuffbox">

						<h3><?php _e('Receipt Email (Default)'); ?></h3>

						<div class="inside">

							<p>
								You can use the following replacement strings.<br>
								<strong><?php foreach ($fields as $field) echo "[$field] "; ?></strong>
							</p>

							<label for="payments_notifications_receipt_subject"><strong>Subject</strong></label><br>
							<input type="text" class="regular-text" id="payments_notifications_receipt_subject" name="payments_notifications_receipt_subject" value="<?php echo esc_attr( get_option('payments_notifications_receipt_subject') ); ?>">
							<?php
								$settings = array(
									'teeny' => true,
									'textarea_rows' => 15,
									'tabindex' => 1,
									'media_buttons' => true,
									'textarea_name' => 'payments_notifications_receipt',
									'wpautop' => false
								);
								wp_editor(get_option('payments_notifications_receipt'), 'payments_notifications_receipt', $settings);
							?>

						</div>

					</div><!-- .stuffbox -->

					<div class="stuffbox">

						<h3><?php _e('Failed Recurring Payment'); ?></h3>

						<div class="inside">

							<p>
								You can use the following replacement strings.<br>
								<strong><?php foreach ($fields as $field) echo "[$field] "; ?></strong>
							</p>

							<label for="payments_notifications_failed_subject"><strong>Subject</strong></label><br>
							<input type="text" class="regular-text" id="payments_notifications_failed_subject" name="payments_notifications_failed_subject" value="<?php echo esc_attr( get_option('payments_notifications_failed_subject') ); ?>">
							<?php
								$settings = array(
									'teeny' => true,
									'textarea_rows' => 15,
									'tabindex' => 2,
									'media_buttons' => true,
									'textarea_name' => 'payments_notifications_failed',
									'wpautop' => false
								);
								wp_editor(get_option('payments_notifications_failed'), 'payments_notifications_failed', $settings);
							?>

						</div>

					</div><!-- .stuffbox -->

                    <div class="stuffbox">

						<h3><?php _e('Card Expiry Warning Email'); ?></h3>

						<div class="inside">

                            <p>This email is sent on the first day of the month a card is due to expire.</p>

							<p>
								You can use the following replacement strings.<br>
								<strong><?php foreach ($fields as $field) echo "[$field] "; ?></strong>
							</p>

							<label for="payments_notifications_expirywarning_subject"><strong>Subject</strong></label><br>
							<input type="text" class="regular-text" id="payments_notifications_expirywarning_subject" name="payments_notifications_expirywarning_subject" value="<?php echo esc_attr( get_option('payments_notifications_expirywarning_subject') ); ?>">
							<?php
								$settings = array(
									'teeny' => true,
									'textarea_rows' => 15,
									'tabindex' => 2,
									'media_buttons' => true,
									'textarea_name' => 'payments_notifications_expirywarning',
									'wpautop' => false
								);
								wp_editor(get_option('payments_notifications_expirywarning'), 'payments_notifications_expirywarning', $settings);
							?>

						</div>

					</div><!-- .stuffbox -->

                    <div class="stuffbox">

						<h3><?php _e('Card Expired Email'); ?></h3>

						<div class="inside">

                            <p>This email is sent immediately after a supporter's card has expired.</p>

							<p>
								You can use the following replacement strings.<br>
								<strong><?php foreach ($fields as $field) echo "[$field] "; ?></strong>
							</p>

							<label for="payments_notifications_expiry_subject"><strong>Subject</strong></label><br>
							<input type="text" class="regular-text" id="payments_notifications_expiry_subject" name="payments_notifications_expiry_subject" value="<?php echo esc_attr( get_option('payments_notifications_expiry_subject') ); ?>">
							<?php
								$settings = array(
									'teeny' => true,
									'textarea_rows' => 15,
									'tabindex' => 2,
									'media_buttons' => true,
									'textarea_name' => 'payments_notifications_expiry',
									'wpautop' => false
								);
								wp_editor(get_option('payments_notifications_expiry'), 'payments_notifications_expiry', $settings);
							?>

						</div>

					</div><!-- .stuffbox -->



					<?php submit_button(); ?>

				</form>

			</div><!-- post-body-content -->


		</div><!-- #post-body .metabox-holder .columns-2 -->

		<br class="clear">

	</div><!-- #poststuff -->

</div> <!-- .wrap -->
