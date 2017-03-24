<?php
/**
 * Plugin Options Page
 *
 */
 ?>
<div class="wrap">

    <h2><?php _e('Payments'); ?></h2>

	<div>

		<div id="post-body" class="columns-3">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box">

					<form method="post" action="options.php">

    					<?php settings_fields('payments_options'); ?>
    					<?php do_settings_sections('payments_options'); ?>

    					<h3><?php _e('Statement'); ?></h3>

        				<hr>

    					<div class="option">
				            <p><b><label for="payments_statement_discriptor">Default Statement Discriptor</label></b></p>
				            <p><p><input type="text" class="regular-text" id="payments_statement_discriptor" name="payments_statement_discriptor" value="<?php echo esc_attr( get_option('payments_statement_discriptor') ); ?>"></p>
				        </div>


    					<?php do_action('payment_custom_options'); ?>
    					<?php submit_button(); ?>

					</form>

				</div><!-- .meta-box -->

			</div><!-- post-body-content -->


		</div><!-- #post-body .metabox-holder .columns-2 -->

		<br class="clear">

	</div><!-- #poststuff -->

</div> <!-- .wrap -->