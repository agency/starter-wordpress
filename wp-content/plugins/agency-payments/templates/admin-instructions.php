
<div class="wrap">

    <h2><?php _e('Payment Instructions'); ?></h2>
    <p><em>A how-to guide to setting up Payments on your site.</em></p>

    <hr>

	<div id="poststuff">

		<div id="post-body" class="">

			<div class="section stuffbox">

				<div class="inside">
			
					<h3>Getting Started</h3>
					<hr>
					<p>With Payments you can easily setup up multiple appeals and allow your users to make once-off and recurring donations.</p>

				</div>

			</div>

			<hr>

			<div class="section stuffbox">

				<div class="inside">
			
					<h3>Appeals</h3>
					<hr>
					<p>Appeals are the backbone of the Payments Plugin. They are where you define all of the details for a specific donation.</p>
					<p><b>Form</b></p>
					<p>The form fields define the once-off and recurring amounts that will display on the form.</p>
					<p><b>Completion</b></p>
					<p>Define the response the user will get when they complete a donation.</p>
					<p><b>Notification</b></p>
					<p>Define a custom email to send to the user when they make a donation to this appeal. If left blank the default email will send.</p>
					<p><b>Settings</b></p>
					<p>Assign a fund code to pass through to the transaction</p>

				</div>

			</div>

			<div class="section stuffbox">

				<div class="inside">
			
					<h3>Notifications</h3>
					<hr>
					<p><b>Email Settings</b></p>
					<p>Define an email that will receive a copy of all emails sent by the plugin.</p>

					<p><b>Using Replacements</b></p>
					<p>There are a list of replacements that can be used in the email that will be swapped out with data from the form. For example, if your write "Dear [first_name]" that will read in the "Dear Paul" if Paul is the user's first name.</p>
					<p><b>Default Receipt Email</b></p>
					<p>This is the default email that a user will recieve when they make a donation.</p>

					<p><b>Failed Recurring Payment</b></p>
					<p>This email will be triggered when a user's payment fails</p>

					<p><b>Card Expiry Warning</b></p>
					<p>This email will be triggered the month before a user's card expires.</p>

					<p><b>Card Expired</b></p>
					<p>This email will send when a user's card expires.</p>

				</div>

			</div>

			<div class="section stuffbox">

				<div class="inside">
			
					<h3>Transactions</h3>
					<hr>
					<p>Transactions are a list of successful payments and the data associated with them</p>
					<p>You can search transaction by a user's email address and there is an option to resent the receipt to them from within the transaction.</p>

				</div>

			</div>

			<div class="section stuffbox">

				<div class="inside">
			
					<h3>Subscriptions</h3>
					<hr>
					<p>Subscriptions define a recurring payment. From within a Subscription you can set the amount to charge each month and the date of the next time the charge will run.</p>

				</div>

			</div>

			



			<?php do_action('payments_instructions'); ?>

		</div><!-- #post-body .metabox-holder .columns-2 -->

		<br class="clear">



	</div><!-- #poststuff -->

	<hr>

	<h2><?php _e('Payment Gateways'); ?></h2>

	<hr>

	<div id="poststuff">

		<?php do_action('payments_instructions_gateways'); ?>

	</div>

</div> <!-- .wrap -->
