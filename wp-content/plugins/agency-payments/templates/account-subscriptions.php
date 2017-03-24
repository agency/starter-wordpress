<?php $subscriptions = get_user_subscriptions(); ?>

<div class="payments-account">

	<h1>Subscriptions</h1>

	<?php if($subscriptions) : ?>

		<?php foreach($subscriptions as $subscription) : ?>

			<div class="account-subscription">

				<h4><?php echo get_the_title($subscription->appeal_id); ?></h4>

				<div class="amount">
					<b>Amount</b>
					<span class="value">$<?php echo $subscription->amount; ?></span>
				</div>

				<div class="start-date">
					<b>Start Date</b>
					<span class="value"><?php echo date('F j, Y', strtotime($subscription->start_date)); ?></span>
				</div>

				<div class="next-renewal-date">
					<b>Next Renewal Date</b>
					<span class="value"><?php echo date('F j, Y', strtotime($subscription->next_renewal_date)); ?></span>
				</div>

				<?php if($subscription->lapsed_on) : ?>

					<div class="next-renewal-date">
						<b>Payment Lapsed On</b>
						<span class="value"><?php echo date('F j, Y', strtotime($subscription->lapsed_on)); ?></span>
					</div>

				<?php endif; ?>

				<div class="controls">

					<a href="#" class="js-edit-subscription button" data-subscription-edit-form="<?php echo $subscription->ID; ?>">Edit Subscription</a>
					<a href="#" class="js-cancel-subscription button" data-subscription-cancel-form="<?php echo $subscription->ID; ?>">Cancel Subscription</a>

				</div>

				<div class="account-subscription-edit-form" data-account-subscription-edit-form="<?php echo $subscription->ID; ?>">

					<h6>Edit Subscription</h6>
					<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

						<div class="field -half">
					        <label for="payment-amount">Amount</label>
					        <input id="payment-amount" type="number" min="1" name="subscription[amount]" placeholder="Amount" value="<?php echo $subscription->amount; ?>" />
					    </div>
						<input type="hidden" name="subscription[id]" value="<?php echo $subscription->ID; ?>">
						<input type="hidden" name="payments_action" value="edit_subscription">
						<?php wp_nonce_field( 'payments' ); ?>

						<button type="submit">Save Subscription</button>

					</form>

				</div>

				<div class="account-subscription-cancel-form" data-account-subscription-cancel-form="<?php echo $subscription->ID; ?>">

					<h6>Cancel Subscription</h6>
					<p>Are you sure you'd like to cancel your subscription?</p>
					<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

						<input type="hidden" name="subscription[id]" value="<?php echo $subscription->ID; ?>">
						<input type="hidden" name="payments_action" value="cancel_subscription">
						<?php wp_nonce_field( 'payments' ); ?>

						<button type="submit primary">Cancel Subscription</button>

					</form>

				</div>

			</div>

		<?php endforeach; ?>

	<?php else : ?>

		<p>You don't have any current subscriptions.</p>

	<?php endif; ?>

</div>