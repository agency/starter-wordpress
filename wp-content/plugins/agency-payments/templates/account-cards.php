<?php $subscriptions = get_user_subscriptions(); ?>

<?php $cards = array(); ?>

<div class="payments-account">

	<h1>Credit Cards</h1>
	<p>Update their credit card details for a donation.</p>

	<?php $i = 0; foreach($subscriptions as $subscription) : ?>

		<?php if($subscription->gateway != 'stripe') continue; // Only Available for Stripe ?>

		<?php $sources = get_gateway_sources($subscription);  ?>

		<?php foreach($sources as $source) :  ?>

			<?php if($cards[$source->id]) continue; ?>

			<?php $cards[$source->id] = $source; ?>

			<div class="account-source">

				<span class="text">Credit Card</span>
				<span class="number">(.... .... .... <?php echo $source->last4; ?>)</span>

				<a href="#" class="js-edit-card" data-subscription-edit-card-form="card-<?php echo $i; ?>">Edit Card</a>

				<div class="account-subscription-edit-card-form" data-account-subscription-edit-card-form="card-<?php echo $i; ?>">

					<h6>Edit Source</h6>

					<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
						<div class="errors"></div>
						<?php the_payment_card_inputs($source); ?>

						<input type="hidden" name="payment[card][id]" value="<?php echo $source->id; ?>">
						<input type="hidden" name="subscription[id]" value="<?php echo $subscription->ID; ?>">
						<input type="hidden" name="payments_action" value="edit_source">
						<?php wp_nonce_field( 'payments' ); ?>

						<button type="submit" data-payment-gateway-edit-card="<?php echo $subscription->gateway; ?>">Save Card</button>

					</form>

				</div>



			</div>

		<?php $i++; endforeach; ?>


	<?php endforeach; ?>

</div>