<fieldset class="fieldset-three">

	<?php global $current_user; ?>
	<?php if($current_user) : $sources = get_sources(); ?>

		<?php if($sources) : ?>

			<div class="payments-saved-cards">

				<h6>Use an existing card</h6>

				<?php foreach($sources as $id=>$source) : ?>

					<div class="field -full">
					    <input id="<?php echo $id; ?>" type="radio" name="payment[customer_id]" placeholder="0000 0000 0000 0000" value="<?php echo $source['customer_id']; ?>" required />
					    <label for="<?php echo $id; ?>">XXXX XXXX XXXX <?php echo $source['source']->last4; ?></label>
					</div>

				<?php endforeach; ?>

				<div class="field -full">
				    <input id="payment-new-card" type="radio" name="payment[customer_id]" placeholder="0000 0000 0000 0000" value="newcard" required="" />
				    <label for="payment-new-card">New Card</label>
				</div>

			</div>

			<div class="js-payments-new-card">

				<h6>New Card</h6>

		<?php endif; ?>

	<?php endif; ?>

    <?php the_payment_card_inputs(); ?>

    <?php if($sources) : ?></div><?php endif; ?>

</fieldset>