<div class="subscription-metabox">

	<?php $subscription = new \Payments\Subscription($_GET['post']); ?>

	<?php if(!empty($subscription->meta)) : ?>

		<table border="0" cellpadding="2" cellspacing="2">

		<?php foreach($subscription->meta as $key=>$value) : ?>

			<tr>
				<th align="left"><?php echo $key; ?>:</th>
				<td align="left"><?php echo $value; ?></td>
			</tr>

		<?php endforeach; ?>

		</table>

	<?php else : ?>

		<p>No custom data assigned to this subscription.</p>

	<?php endif; ?>

</div>