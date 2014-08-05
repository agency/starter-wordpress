<?php
/**
 * 	@package Deploy
 */

/*
	Plugin Name: Deploy
	Description: Code-based options synchronisation to manage WordPress from development &rsaquo; staging &rsaquo; production.
	Version: 0.2
	Author: Agency
	Author URI: http://agency.sc
	License: GPLv2 or later
*/

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( !function_exists( 'add_action' ) ) {
	echo 'Awkward. I\'m just a plugin.';
	exit;
}
 

/**
 * Constants / Settings
 *
 * @package deploy
 * @since 0.1
 *         
 */

$deploy_notices = array();
$deploy_options = get_option('deploy_options');

if (!isset($deploy_options['config_dir'])) {
	$deploy_options['config_dir'] = '/wp-content/deploy';
}

define('DEPLOY_DATA_DIR', rtrim(ABSPATH, '/') . $deploy_options['config_dir']);
define('DEPLOY_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('DEPLOY_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('DEPLOY_DATA_FILE', DEPLOY_DATA_DIR . '/deploy.conf');
define('DEPLOY_PLUGIN_VERSION', '0.1');


/**
 * Basic initialisation & setup
 *
 * @package deploy
 * @since 0.1
 *         
 */

register_activation_hook( __FILE__, 'deploy_activate' );
function deploy_activate() {
	
	deploy_reset();
	
}

add_filter('plugin_action_links', 'deploy_plugin_settings_link', 10, 2);
function deploy_plugin_settings_link($links, $file) {
 
    if ( $file == 'deploy/deploy.php' ) {
        $links['settings'] = sprintf( '<a href="%s"> %s </a>', admin_url( 'options-general.php?page=deploy' ), __( 'Settings', 'deploy' ) );
    }
    return $links;
 
}

// actions to the plugin page

add_action('admin_init', 'deploy_init');
function deploy_init() {
	
	// register options
	register_setting( 'deploy_plugin_options', 'deploy_options' );

	// stop if not the settings page
	if ($_GET['page'] !== 'deploy') return;

	if ($_GET['reset'] == 'true' || !file_exists(DEPLOY_DATA_FILE)) {
		deploy_reset();
	}

	if (isset($_POST['deploy'])) {
		deploy_sync($_POST['deploy']);
	}

}

// options

add_action('admin_menu', 'deploy_options');
function deploy_options()  {  
    $settings = add_options_page( __('Deploy','deploy'), __('Deploy','deploy'), 'manage_options', 'deploy','deploy_options_page');  
    add_action( 'admin_print_styles-' . $settings, 'deploy_admin_styles' );
} 

function deploy_options_page() {
	require 'deploy-options.php';
}

function deploy_admin_styles() {
	wp_enqueue_style( 'deploy-admin', DEPLOY_PLUGIN_URL . 'deploy.css' );
	wp_enqueue_script( 'deploy-js', DEPLOY_PLUGIN_URL . 'deploy.js', array('jquery'), '1.0', TRUE );
}


// language

add_action('plugins_loaded', 'deploy_language');
function deploy_language() {
	load_plugin_textdomain( 'deploy', false, DEPLOY_PLUGIN_DIR );
}


// notices

add_action('admin_notices', 'deploy_notices');
function deploy_notices() {
	
	if ($_GET['debug'] == 'true') {
		echo '<pre>';
		print_r(deploy_get_data());
		echo '</pre>';
	}

	global $deploy_notices;
	if ($deploy_notices) {
		foreach ($deploy_notices as $notice) {
			echo '<div class="updated"><p><strong>' . $notice . '</strong></p></div>';
		}
	}
	
}



/**
 * Manipulation functions
 *
 * @package deploy
 * @since 0.1
 *         
 */

// parse data file

function deploy_get_data() {
	
	if (!file_exists(DEPLOY_DATA_FILE)) return;
	
	require DEPLOY_DATA_FILE;
	return $deploy_data;
		
}

// overwrites data file with database

function deploy_reset() {
	$wp_options = wp_load_alloptions();
	$deploy_data = array();

	foreach( $wp_options as $name => $value ) {
		if (substr($name,0,1) !== '_' && strlen($value) > 0 && $name !== 'cron')
			$deploy_data['wp_options'][$name] = $value;
	}

	write_data($deploy_data, DEPLOY_DATA_FILE);

	global $deploy_notices;
	$deploy_notices[] = 'Deploy data reset from database';

}

// updates database data

function deploy_sync($fields) {

	if (!is_array($fields)) return;
	$deploy_data = deploy_get_data();

	foreach ($fields as $field) {
		$data = $deploy_data['wp_options'][$field];
		if (unserialize($data)) {
			$data = unserialize($data);
		}
		update_option($field, $data);
	}

	global $deploy_notices;
	$deploy_notices[] = 'Data successfully synchronised';

}


/**
 * Update ini with changed option
 *
 * @package deploy
 * @since 0.1
 *         
 */

add_action('updated_option', 'deploy_option_update');
function deploy_option_update($option) {

	if (substr($option,0,1) == '_' || $name == 'cron') return false;

	$deploy_data = deploy_get_data();

	$value = get_option($option);
	if (gettype($value) == 'array' || gettype($value) == 'object') $value = serialize($value);

	$deploy_data['wp_options'][$option] = $value;

	write_data($deploy_data, DEPLOY_DATA_FILE);

}


/**
 * Compare data file with database, output array of difs
 *
 * @package deploy
 * @since 0.1
 *         
 */

function deploy_get_difs() {

	$diff = array();
	$deploy_data = deploy_get_data();

	foreach($deploy_data as $section => $data) {
		$diff[$section] = array();
		
		foreach ($data as $key => $value) {
			
			$db_value = get_option($key);

			if (gettype($db_value) == 'array' || gettype($db_value) == 'object') {
				$db_value = serialize($db_value);
			}

			if (gettype($value) == 'array' || gettype($value) == 'object') {
				$value = serialize($value);
			}

			similar_text(trim($db_value), trim($value), $percent);

			if ($percent < 99) {
				$diff[$section][$key] = array(
					'file' => $value,
					'database' => $db_value
				);
			}

		}
	}

	return $diff;

}


/**
 * Helper -> Write array to ini file.
 *
 * @package deploy
 * @since 0.1
 *         
 */

function write_data($array, $file) {
    
    if (!is_array($array)) return false;

    $file_header = "<?php\r\n\r\n";
    $file_header .= "// Deploy Data: Plugin Version " . DEPLOY_PLUGIN_VERSION . "\r\n";
    $file_header .= "// Last updated: " . date('r') . "\r\n\r\n";

    $data = var_export($array, true);

    if (!is_dir(DEPLOY_DATA_DIR)) mkdir(DEPLOY_DATA_DIR);

    if (is_writeable(DEPLOY_DATA_DIR)) file_put_contents($file, $file_header . "\$deploy_data = $data;");

}