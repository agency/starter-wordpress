<div class="payments-form-wrap" data-payments-form>

	<div class="navigation">
		<span class="navigation-title">Donate to Us</span>
		<span class="active" data-payment-control aria-label="Step 1">1</span>
		<span data-payment-control aria-hidden="true">2</span>
		<span data-payment-control aria-hidden="true">3</span>

	</div>

	<form class="payments-form -step-1" action="/" method="post">

		<div class="errors"></div>

		<div class="screen active" data-payment-control>

			<?php do_action('payments_form_screen_one', $appeal); ?>

		</div>

		<div class="screen" data-payment-control>

			<?php do_action('payments_form_screen_two',$appeal); ?>

		</div>

		<div class="screen" data-payment-control>

			<?php do_action('payments_form_screen_three',$appeal); ?>

		</div>

		<?php do_action('payments_form_before_submit'); ?>

		<input type="hidden" name="payment[fund]" value="<?php the_field('donation_fund',$appeal['id']); ?>"/>
		<input type="hidden" name="payment[amount]" value=""/>
		<input type="hidden" name="payment[id]" value="<?php echo $appeal['id']; ?>"/>

		<input type="hidden" name="payments_action" value="process_payment"/>
		<?php wp_nonce_field( 'payments' ); ?>

		<div class="footer-navigation">

			<a href="#prev" class="button -prev" data-payment-control="prev" style="display:none;" role="button" aria-label="Go to previous step">Prev</a>
			<a href="#next" class="button -next" data-payment-control="next" role="button" aria-label="Go to next step">Next</a>
			<button type="submit" data-payment-submit style="display:none;" data-payment-gateway='stripe' disabled="disabled">Donate</button>

		</div>

	</form>

	<?php do_action('payments_form_screen_processing',$appeal); ?>



</div>