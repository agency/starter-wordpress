<?php
/**
 * @wordpress-plugin
 * Plugin Name: CCP Content Structure
 * Description: Content Taxonomy Structure & Custom Post Types
 * Author: Agency
 * Version: 1.0
 * Author URI: http://agency.sc/
 */

if (!defined('ABSPATH')) { exit; }


class CPP_Content_Structure {

   /**
	* Contruct
	* ---------------------------------------------
	* @return false
	* ---------------------------------------------
	**/

	function __construct() {

		// Actions
		add_action( 'init', array($this, 'setup'), 10, 0 );
		add_action( 'pre_get_posts', array($this,'pre_get_partner') );
		add_action( 'parse_request', array($this , 'custom_url_paths') );
		// add_action('parse_request', array($this , 'get_json'));
		

	}

   /**
	* Setup
	* ---------------------------------------------
	* @return false
	* ---------------------------------------------
	**/

	public function setup() {

		$this->register_types();

		$this->register_taxonomies();

	}

   /**
	* Register Taxonomies
	* ---------------------------------------------
	* @return false
	* ---------------------------------------------
	**/

	public function register_taxonomies() {

		// ------------------------------------
		// Partner Categories
		// ------------------------------------

		$partner_tax_labels = array(
			'name'                       => _x( 'Partner Categories', 'taxonomy general name' ),
			'singular_name'              => _x( 'Partner Category', 'taxonomy singular name' ),
			'search_items'               => __( 'Search Partner Categories' ),
			'popular_items'              => __( 'Popular Partner Categories' ),
			'all_items'                  => __( 'All Partner Categories' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Partner Category' ),
			'update_item'                => __( 'Update Partner Category' ),
			'add_new_item'               => __( 'Add New Partner Category' ),
			'new_item_name'              => __( 'New Partner Category' ),
			'separate_items_with_commas' => __( 'Separate Partner Categories with commas' ),
			'add_or_remove_items'        => __( 'Add or remove Partner Category' ),
			'choose_from_most_used'      => __( 'Choose from the most used Partner Categories' ),
			'not_found'                  => __( 'No Partner Categories found.' ),
			'menu_name'                  => __( 'Partner Categories' ),
		);

		$partner_tax_args = array(
			'hierarchical'          => true,
			'labels'                => $partner_tax_labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'partner-category' ),
		);

		// register_taxonomy( 'partner-category', 'partner', $partner_tax_args );
		



	}

  /**
	* Build Taxonomy Labels
	* ---------------------------------------------
	* @param  $name   | String | Singular Name
	* @param  $plural | String | Plural Name
	* @return false
	* ---------------------------------------------
	**/

	private static function build_taxonomy_labels($single, $plural = false) {

		if(!$plural) $plural = $single . 's';

		$labels = array(
			'name'                       => ucfirst($plural),
			'singular_name'              => ucfirst($single),
			'search_items'               => 'Search ' . ucfirst($plural),
			'popular_items'              => 'Popular ' . ucfirst($plural),
			'all_items'                  => 'All ' . ucfirst($plural),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => 'Edit ' . ucfirst($single),
			'update_item'                => 'Update ' . ucfirst($single),
			'add_new_item'               => 'Add New ' . ucfirst($single),
			'new_item_name'              => 'New Writer ' . ucfirst($single),
			'separate_items_with_commas' => 'Separate ' . ucfirst($plural) . ' with commas',
			'add_or_remove_items'        => 'Add or remove ' . $plural,
			'choose_from_most_used'      => 'Choose from the most used ' . $plural,
			'not_found'                  => 'No ' . $plural . ' found',
			'menu_name'                  => ucfirst($plural),
		);

		return $labels;

	}


  /**
	* Register Types
	* ---------------------------------------------
	* @return false
	* ---------------------------------------------
	**/

	public function register_types() {


		// ------------------------------------
		// Parks
		// ------------------------------------

		$args = array(
			'labels'             	=> self::build_type_labels('Partner', 'Partners'),
			'public'             	=> true,
			'publicly_queryable' 	=> true,
			'show_ui'            	=> true,
			'show_in_menu'       	=> true,
			'query_var'          	=> true,
			'rewrite'            	=> array( 'slug' => 'partners' ),
			'capability_type'    	=> 'post',
			'has_archive'        	=> true,
			'hierarchical'       	=> false,
			'menu_icon'					 	=> 'dashicons-post-status',
			'menu_position'      	=> 40,
			'supports'           	=> array( 'title', 'excerpt', 'editor', 'thumbnail', 'comments' )
		);
		register_post_type( 'partner', $args );


	}

   /**
	* Build Type Labels
	* ---------------------------------------------
	* @param  $name   | String | Singular Name
	* @param  $plural | String | Plural Name
	* @return false
	* ---------------------------------------------
	**/

	private static function build_type_labels($name, $plural) {

		return array(
			'name'               	=> $plural,
			'singular_name'      	=> $name,
			'add_new'            	=> "Add New",
			'add_new_item'       	=> "Add New $name",
			'edit_item'          	=> "Edit $name",
			'new_item'           	=> "New $name",
			'all_items'          	=> "All $plural",
			'view_item'          	=> "View $name",
			'search_items'       	=> "Search $plural",
			'not_found'          	=> "No " . strtolower($plural) . " found",
			'not_found_in_trash' 	=> "No " . strtolower($plural) . " found in trash",
			'parent_item_colon'  	=> '',
			'menu_name'          	=> $plural
		);

	}

	public function get_partner(){

		$partner = get_posts(array('post_type'=>'partner','post_status'=>'publish','posts_per_page'=>'-1'));
		$data = array();
		foreach($partner as $p){

			$partner 								= array();
			$partner['title'] 			= $p->post_title;
			$partner['description'] = $p->post_excerpt;
			$partner['location'] 		= get_field('partner_gmap',$p->ID);
			$partner['postcodes'] 	= get_field('partner_postcodes',$p->ID);
			$partner['link'] 				= get_permalink($p->ID);
			$partner['status'] 			= get_field('partner_status',$p->ID);
			
			$data[] = $partner;

		}
		return $data;

	}


	// @todo get working
	public function postcode_lookup(){

		$partner = get_posts(array('post_type'=>'partner','post_status'=>'publish','posts_per_page'=>'-1'));
		$data = array();
		$match = array();
		
		foreach($partner as $p){

			$partner 								= array();
			$partner['title'] 			= $p->post_title;
			$partner['description'] = $p->post_excerpt;
			$partner['postcodes'] 	= get_field('partner_postcodes',$p->ID);
			// $partner['postcodes'] 	= explode(" ",get_field('partner_postcodes',$p->ID));
			$partner['link'] 				= get_permalink($p->ID);
			$partner['status'] 			= get_field('partner_status',$p->ID);


			$data[] = $partner;


		}

		if (!empty($_GET['postcode'])) {

	  	$postcode = strval(intval($_GET['postcode']));
	  	
	  	// foreach( $data as $d ) {

	  	// }

		  $results  = array();
	    $hasValue = false;

	    foreach ($data as $subarray) {
	      $hasValue = false;
	      foreach($subarray as $value){
	        if(is_string($value) && strpos($value,$postcode) !== false)
	          $hasValue = true;
	      }
	      if($hasValue)
	      $results[] = $subarray;
	    }

		 	if( !empty($results)) {
		 		return $results;
		 	}
		}
	}


	/**
	 * Pre Get Parks
	 * Always return all of the partner
	 * @param object $query The Wordpress Query Object
	 * @return null
	 */

	public function pre_get_partner( $query ) {

		if ( !is_admin() && $query->is_main_query() ) {

			if( $query->is_archive && isset($query->query['post_type']) ) {

				if( $query->query['post_type'] == 'partner') {

					$query->set('posts_per_page', -1);

				}

				if( $query->query['post_type'] == 'location') {

					$query->set('posts_per_page', -1);

				}
			}

		}

	}

	/**
	 * Custom URL Paths
	 * Api endpoints
	 * @param object $wp
	 * @return array
	 */

	public function custom_url_paths($wp) {

		$pagename = (isset($wp->query_vars['pagename'])) ? $wp->query_vars['pagename'] : $wp->request;

		switch ($pagename) {

			case 'api/partners':
				$partner = $this->get_partner();
				$this->output_json($partner);
				break;
			case 'api/partners/search':
				$partner = $this->postcode_lookup();
				$this->output_json($partner);
				break;
			default:
				break;
		}

	}

	/**
	 * Output JSON
	 *
	 * @param $array Array to encode
	 */

	public function output_json($array) {

		header('Content-type: application/json');
		echo json_encode($array);
		exit();

	}

}

$cpp_content_structure = new CPP_Content_Structure();