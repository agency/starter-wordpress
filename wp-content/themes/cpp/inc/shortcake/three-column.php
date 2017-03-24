<?php
// ------------------------------------
//
// Three Column Insert
//
// ------------------------------------

add_shortcode( 'threecolumninsert', function( $attr, $content = '' ) {

	ob_start();

	
	$theme = $attr['theme'];

	// Build An Array
	$columns = array();
	$columns[0] = array();
	$columns[0]['image_id']  = $attr['attachment_id_one'];
	$columns[0]['title']     = $attr['title_one'];
	$columns[0]['text']      = $attr['text_one'];
	$columns[0]['link']      = $attr['link_one'];
	$columns[0]['link_text'] = $attr['link_text_one'];
	$columns[1]['image_id']  = $attr['attachment_id_two'];
	$columns[1]['title']     = $attr['title_two'];
	$columns[1]['text']      = $attr['text_two'];
	$columns[1]['link']      = $attr['link_two'];
	$columns[1]['link_text'] = $attr['link_text_three'];
	$columns[2]['image_id']  = $attr['attachment_id_three'];
	$columns[2]['title']     = $attr['title_three'];
	$columns[2]['text']      = $attr['text_three'];
	$columns[2]['link']      = $attr['link_three'];
	$columns[2]['link_text'] = $attr['link_text_three'];

	$columns[3]['action_button_link'] = $attr['action_button_link'];
	$columns[3]['action_button_text'] = $attr['action_button_text'];

	?>


	<div class="columns-insert columns-3 <?php if ($theme) echo "-".$theme; ?>">
		
		<?php foreach($columns as $column) : ?>
			
			<?php 
				$image_id = $column['image_id'];
				$title = $column['title'];
				$text = $column['text'];
				$link = $column['link'];
				$link_text = $column['link_text'];
			?>

			<?php if(!empty($image_id) || !empty($title) || !empty($text)) : ?>

				<article class="column">

					<div class="image">

						<?php if(!empty($link)) : ?><a href="<?php echo $link; ?>"><?php endif; ?>

						<img src="<?php the_attachement_src($image_id,'full'); ?>">

						<?php if(!empty($link)) : ?></a><?php endif; ?>

					</div>

					<?php if ($title) : ?><div class="title"><?php echo $title; ?></div><?php endif; ?>

					<div class="text"><?php echo $column['text']; ?></div>
					
					<?php if(!empty($link) && !empty($link_text)) : ?>
						
							<a href="<?php echo $link ?>" class="button button-transparent"><?php echo $link_text ?></a>

					<?php endif; ?>
			

				</article>

			<?php endif; ?>

		<?php endforeach; ?>
		
		<?php 
			$action_button_link = $column['action_button_link'];
			$action_button_text = $column['action_button_text'];


			if(!empty($action_button_link) && !empty($action_button_text)) : ?>

				<a href="<?php echo $action_button_link ?>" class="button button-transparent -action"><?php echo $action_button_text ?></a>

			<?php endif;
		?>


	</div>

	<?php
	wp_reset_postdata();
	return ob_get_clean();
} );


// Add UI ----------------------------------

if (function_exists('shortcode_ui_register_for_shortcode')) {

	shortcode_ui_register_for_shortcode(
		'threecolumninsert',
		array(
			'label' => 'Three Column Insert',
			'listItemImage' => 'dashicons-images-alt',
			'post_type'     => array( 'page', 'appeal' ),
			'attrs' => array(
				array(
				    'label' => 'Theme',
				    'attr' => 'theme',
				    'type' => 'select',
				    'options' => array(
				        'dark' => 'Dark (Insert white images)',
				        'light' => 'Light (Insert black images)'
				    ),
				),
				array(
					'label'    => 'Image One',
					'attr'     => 'attachment_id_one',
					'type'     => 'attachment'
				),

				array(
					'label' => 'Title One',
					'attr'  => 'title_one',
					'type'  => 'text'
				),

				array(
					'label' => 'Text One',
					'attr'  => 'text_one',
					'type'  => 'text'
				),

				array(
					'label' => 'Link One',
					'attr'  => 'link_one',
					'type'  => 'text'
				),

				array(
					'label' => 'Link Text One',
					'attr'  => 'link_text_one',
					'type'  => 'text'
				),

				array(
					'label'    => 'Image Two',
					'attr'     => 'attachment_id_two',
					'type'     => 'attachment'
				),

				array(
					'label' => 'Title Two',
					'attr'  => 'title_two',
					'type'  => 'text'
				),

				array(
					'label' => 'Text Two',
					'attr'  => 'text_two',
					'type'  => 'text'
				),

				array(
					'label' => 'Link Two',
					'attr'  => 'link_two',
					'type'  => 'text'
				),

				array(
					'label' => 'Link Text Two',
					'attr'  => 'link_text_two',
					'type'  => 'text'
				),

				array(
					'label'    => 'Image Three',
					'attr'     => 'attachment_id_three',
					'type'     => 'attachment'
				),

				array(
					'label' => 'Title Three',
					'attr'  => 'title_three',
					'type'  => 'text'
				),

				array(
					'label' => 'Text Three',
					'attr'  => 'text_three',
					'type'  => 'text'
				),

				array(
					'label' => 'Link Three',
					'attr'  => 'link_three',
					'type'  => 'text'
				),

				array(
					'label' => 'Link Text Three',
					'attr'  => 'link_text_three',
					'type'  => 'text'
				),

				array(
					'label' => 'Action Button Link (Optional - will appear at the bottom)',
					'attr'  => 'action_button_link',
					'type'  => 'text'
				),

				array(
					'label' => 'Action Button Text',
					'attr'  => 'action_button_text',
					'type'  => 'text'
				),

			),
		)
	);

}