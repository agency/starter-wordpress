<?php

function base_responsive_embeds($html, $url, $attr, $post_id) {
	return '<div class="embed-container">' . $html . '</div>';
}
add_filter('embed_oembed_html', 'base_responsive_embeds', 99, 4);
