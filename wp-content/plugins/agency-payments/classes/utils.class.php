<?php

namespace Payments;

class Utils {


	/**
	 * Build Type Labels
	 * Reusable type label building
	 * @param string $name
	 * @param string $plural
	 * @return array
	 */

	public static function build_type_labels($name, $plural) {

		return array(
			'name'               => $plural,
			'singular_name'      => $name,
			'add_new'            => "Add New",
			'add_new_item'       => "Add New $name",
			'edit_item'          => "Edit $name",
			'new_item'           => "New $name",
			'all_items'          => "All $plural",
			'view_item'          => "View $name",
			'search_items'       => "Search $plural",
			'not_found'          => "No " . strtolower($plural) . " found",
			'not_found_in_trash' => "No " . strtolower($plural) . " found in trash",
			'parent_item_colon'  => '',
			'menu_name'          => $plural
		);

	}


    /**
     * Email
     * Sends emails
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param array $replacements
     * @return null
     */

	public function email($to, $subject, $message, $replacements = array()) {

		//replacements
		foreach ($replacements as $variable => $replacement) {
			$message = str_replace($variable, $replacement, $message);
			$subject = str_replace($variable, $replacement, $subject);
		}

		//Send from the site email
		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>'
		);

		if ($bcc = get_option('payments_notifications_bcc')) $headers[] = 'Bcc: ' . $bcc;

		//WP mail function
		wp_mail( $to, $subject, $message , $headers);

	}

	/**
	 * Output CSV
	 * Generates a csv to download
	 * @param array $array
	 * @param string $filename the file name
	 * @return csv dowload
	 */

	public static function output_csv($array, $filename = 'report.csv') {

		ob_clean();
		ob_start();

		$file = fopen('php://output', 'w');

		// generate csv lines from the inner arrays
		$headings = array();
		foreach ($array[0] as $key => $line) {
			$headings[] = $key;
		}

		fputcsv($file, $headings);
		foreach($array as $row) {
		    fputcsv($file, $row);
		}

	    // rewind file
	    $output = stream_get_contents($file);
	    fclose($file);

	    // prep download
	    header("Content-type: text/x-csv");
	    header("Content-Transfer-Encoding: binary");
	    header('Content-Disposition: attachement; filename="' . $filename . '";');
	    header("Pragma: no-cache");
	    header("Expires: 0");

	    echo $output;
	    exit();

	}

   /**
    * Redirect
    * ---------------------------------------------
    * @param $path | String/Int | url of post id
    * @return false
    * ---------------------------------------------
    **/

	public static function redirect($path) {

		if(is_numeric($path)){ $path = get_permalink($path); }
		wp_safe_redirect( $path );
	  	exit();

	}

	/**
    * Output JSON
    * ---------------------------------------------
    * @param $array    | Array/Object | Data to output
    * @return false
    * ---------------------------------------------
    **/

	public static function output_json($array) {

		header('Content-type: application/json');
		echo json_encode($array);
		exit();

	}

	public static function template_include($template, $data = null,$name = null){

		$basename = basename($template);

		$theme = get_template_directory() . '/payments/' . $basename;
		$path = (file_exists($theme)) ? $theme : $template;
		if(isset($name)){ ${$name} = $data; }
		include($path);

	}

	public static function trace($val, $title = null){

	    print "<pre>";

	    if($title) echo "<b>$title</b>\n";

	    if(is_array($val)) print_r($val);
	    elseif(is_object($val)) print var_dump($val);
	    else print $val;

	    print "</pre>";
	}

	public static function url(){

		return str_replace('classes/', '', $this->dir = plugin_dir_url(__FILE__));

	}







}

?>