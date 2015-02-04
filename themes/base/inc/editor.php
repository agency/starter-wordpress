<?php

add_editor_style('assets/css/editor.css');

function mce_editor_buttons( $buttons ) {
		array_unshift( $buttons, 'styleselect' );
		return $buttons;
}

add_filter( 'mce_buttons_2', 'mce_editor_buttons' );

function mce_before_init( $settings ) {

		$style_formats = array(
				array(
						'title' => 'Button',
						'selector' => 'a',
						'classes' => 'button button-primary'
				)
		);

		$settings['style_formats'] = json_encode($style_formats);

		return $settings;

}

add_filter('tiny_mce_before_init', 'mce_before_init');
