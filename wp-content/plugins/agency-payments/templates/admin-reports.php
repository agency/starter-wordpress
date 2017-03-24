<?php
/**
 * Plugin Options Page
 *
 */
 ?>
<div class="wrap">

    <h2><?php _e('Payment Reports'); ?></h2>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

			<!-- main content -->
			<div id="post-body-content" class="edit-form-section">

				<form method="post" action="">

					<input type="hidden" name="payments_action" value="export">
					<?php wp_nonce_field('payments'); ?>

					<div class="stuffbox">

						<h3><?php _e('Transaction Exports'); ?></h3>

						<div class="inside">

	    					<table class="form-table">
	    						<tbody>
	    							<tr valign="top">
	    								<td scope="row">From</td>
	    								<td>
											<input type="date" class="regular-text" id="payments_report_from" name="payments_report_from" value="<?php echo $_POST['payments_report_from']; ?>">
										</td>
	    							</tr>
	    							<tr valign="top">
	    								<td scope="row">To</td>
	    								<td>
											<input type="date" class="regular-text" id="payments_report_to" name="payments_report_to" value="<?php echo $_POST['payments_report_to']; ?>">
										</td>
	    							</tr>
	    						</tbody>
	    					</table>

						</div>

					</div><!-- .stuffbox -->

					<?php submit_button('Download Transactions'); ?>

				</form>


			</div><!-- post-body-content -->


		</div><!-- #post-body .metabox-holder .columns-2 -->

		<br class="clear">

	</div><!-- #poststuff -->

</div> <!-- .wrap -->
