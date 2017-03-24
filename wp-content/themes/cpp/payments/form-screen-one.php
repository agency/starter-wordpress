<fieldset class="dollar-handles">

	<?php $frequency = get_payments_frequency($appeal['id']); ?>

	<?php if($frequency == 'both') : ?>

		<div class="payment-frequency-toggle" aria-label="Choose donation frequency">

			<div class="field">
				<input type="radio" name="payment[frequency]" id="frequency-single" value="single" tabindex="-1" aria-label="<?php the_field('donation_single_text',$appeal['id']); ?>" checked>
				<label for="frequency-single" tabindex="0" aria-hidden="true"><?php the_field('donation_single_text',$appeal['id']); ?></label>
			</div>

			<div class="field">
				<input type="radio" name="payment[frequency]" id="frequency-month" value="monthly" tabindex="-1" aria-label="<?php the_field('donation_monthly_text',$appeal['id']); ?>">
				<label for="frequency-month" tabindex="0" aria-hidden="true"><?php the_field('donation_monthly_text',$appeal['id']); ?></label>
			</div>

		</div>

	<?php else : ?>

		<input type="hidden" name="payment[frequency]" value="<?php echo $frequency; ?>"/>

	<?php endif; ?>

	<div class="error error-handles"></div>

	<?php $amounts = get_field('donation_amounts',$appeal['id']); ?>

	<?php if($amounts) foreach($amounts as $amount) : ?>

		<a href="#" data-payment-dollar-handle="<?php echo $amount['amount']; ?>" data-payment-frequency="<?php echo $amount['frequency']; ?>" class="dollar-handle <?php echo ($amount['default']) ? 'active' : null; ?>" title="Click to select $<?php echo $amount['amount']; ?>">$<?php echo $amount['amount']; ?></a>

	<?php endforeach; ?>

	<div class="field -other">
		<span class="prefix">$</span>
		<input type="number" min="1" class="other" data-payment-dollar-handle="other" placeholder="Other" aria-label="Enter other amount">
	</div>

	<!-- <input id="frequency" type="checkbox" name="payment[toggle][frequency]" value="monthly"><label for="frequency"><?php the_field('donation_monthly_text',$appeal['id']); ?></label> -->

</fieldset>