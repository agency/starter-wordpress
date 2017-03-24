<?php
// ------------------------------------
//
// Three Column Insert
//
// ------------------------------------

add_shortcode( 'blockquote', function( $attr, $content = '' ) {

	ob_start();
	
	$quote 			 = $attr['quote'];
	$attribution = $attr['attribution'];

	?>

	<blockquote>
		<p><strong>“</strong><?php echo $quote; ?><strong>”</strong></p>
		<span class="attribution"><?php echo $attribution; ?></span>
	</blockquote>


	<?php
	// wp_reset_postdata();
	return ob_get_clean();
} );


// Add UI ----------------------------------

if (function_exists('shortcode_ui_register_for_shortcode')) {

	shortcode_ui_register_for_shortcode(
		'blockquote',

		array(
			'label' 				=> 'Blockquote',
			'listItemImage' => 'dashicons-editor-quote',
			'post_type'     => array( 'page', 'post', 'council' ),
			'attrs' => array(

				array(
					'label' => 'Quote',
					'attr'  => 'quote',
					'type'  => 'textarea'
				),

				array(
					'label' => 'Attribution',
					'attr'  => 'attribution',
					'type'  => 'text'
				),

			),
		)
	);

}