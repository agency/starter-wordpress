<div class="transaction-metabox">

	<?php $tranaction = new \Payments\Transaction($_GET['post']); ?>

	<?php if(!empty($tranaction->meta)) : ?>

		<table border="0" cellpadding="2" cellspacing="2">

		<?php foreach($tranaction->meta as $key=>$value) : ?>

			<tr>
				<th align="left"><?php echo $key; ?>:</th>
				<td align="left"><?php echo $value; ?></td>
			</tr>

		<?php endforeach; ?>

		</table>

	<?php else : ?>

		<p>No custom data assigned to this transaction.</p>

	<?php endif; ?>

</div>