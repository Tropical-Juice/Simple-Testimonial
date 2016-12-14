<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
class Testimonials {
	private static $initiated = false;
	private static $error = array(false, 'All Good');
	
	public static function init() {
		if ( ! self::$initiated ) {
			self::$initiated = true;
			self::init_hooks();
		}
	}
	
	/**
	 * Initializes Plugin
	 */
	private static function init_hooks() {
		self::checkPostData();
		self::registerStylesScripts();
		self::registerPostTypes();
		self::addShortCodes();
		load_plugin_textdomain(TROPICAL_TESTIMONIALS_TEXT_DOMAIN, false, TROPICAL_TESTIMONIALS_PLUGIN_DIR.'/translations/');	
	}
	
	private static function checkPostData(){
		if(isset($_POST['testimonial'])) self::addTestimonial($_POST);
		return true;
	}
	
	private static function addTestimonial($data){
		$score = (float)$data['rating'];
		$formFields = array('organization', 'name', 'function', 'rating');
		if(!self::checkFormData($data, $formFields)){
			self::throwError(__("All fields are required", TROPICAL_TESTIMONIALS_TEXT_DOMAIN));
		}else{
			self::createPost($data);
		}
		return true;
	}
	
	private static function createPost($d){
		global $wp;
		$new_post = array(
			'ID' => '',
			'post_author'  => 2, 
			'post_content' => '', 
			'post_title'   => sprintf( __('Testimonial of %s', TROPICAL_TESTIMONIALS_TEXT_DOMAIN), $d['name']),
			'post_status'  => 'pending',
			'post_type'    => 'testimonials',
			'meta_input'   => array('organization' => $d['organization'],
									'name' => $d['name'],
									'function' => $d['function'],
									'rating' => $d['rating'],
									'testimonial' => $d['testimonial_text'])
        );
		wp_redirect(home_url()."/testimonial/?a=y");
		exit();
	}

	private static function checkFormData($data, $check){
		$error = false;
		foreach($check as $field) {
			if (empty($data[$field])) {
				die(var_dump($data));
				$error = true;
			}
		}
		
		if ($error) {
			return false;
		} else {
			return true;
		}
	}
	
	private static function throwError($msg){
		self:$error = array(true, $msg);
		die(var_dump(self::$error));
	}
	
	public static function addShortCodes(){
		add_shortcode( 'testimonial', array('Testimonials', 'shortcodeTestimonialInput') );
	}
	
	public static function shortcodeTestimonialInput(){
		wp_enqueue_script("RateYo");
		wp_enqueue_script("testimonial-js");
		wp_enqueue_style("RateYo");
		wp_enqueue_style("testimonial-css");
		if(isset($_GET['a'])) $okay = self::getTemplatePart("testimonial-submitted");
		return $okay.self::getTemplatePart("testimonial-form");
	}
	
	public static function registerStylesScripts(){
		//styles
		wp_register_style("RateYo", "//cdnjs.cloudflare.com/ajax/libs/rateYo/2.2.0/jquery.rateyo.min.css", array(), "2.2.0");
		wp_register_style("testimonial-css", TROPICAL_TESTIMONIALS_PLUGIN_URI."assets/css/styles.css", array(), "1.0.0");
		
		//scripts
		wp_register_script("testimonial-js", TROPICAL_TESTIMONIALS_PLUGIN_URI."assets/js/app.js", array(), "1.0.0", true);
		wp_register_script("RateYo", "//cdnjs.cloudflare.com/ajax/libs/rateYo/2.2.0/jquery.rateyo.min.js", array('jquery', 'testimonial-js'), "2.2.0", true);
	}
	
	private static function getTemplatePart($slug, $name = null){
		$templates = array();
	    $name = (string) $name;
	    if ( '' !== $name )
	        $templates[] = "{$slug}-{$name}.php";
	    $templates[] = "{$slug}.php";
	    $template = locate_template($templates, true, false);
		if( !$template ){
			if(!file_exists(dirname(__FILE__) . "/templates/$templates[0]")) return;
			ob_start();
			include( dirname(__FILE__) . "/templates/$templates[0]" );
			return ob_get_clean();
		}
		if( $template ){
			if(!file_exists($template)) return;
			ob_start();
			include( $template );
			return ob_get_clean();
		}
	}
	
	public static function registerPostTypes(){
		$labels = array(
			'name'                  => _x( 'Testimonials', 'Post Type General Name', 'tropical_testimonials' ),
			'singular_name'         => _x( 'Testimonial', 'Post Type Singular Name', 'tropical_testimonials' ),
			'menu_name'             => __( 'Testimonials', 'tropical_testimonials' ),
			'name_admin_bar'        => __( 'Testimonials', 'tropical_testimonials' ),
			'archives'              => __( 'Testimonials', 'tropical_testimonials' ),
			'attributes'            => __( 'Testimonials Attributes', 'tropical_testimonials' ),
			'parent_item_colon'     => __( 'Parent testimonial:', 'tropical_testimonials' ),
			'all_items'             => __( 'All testimonials', 'tropical_testimonials' ),
			'add_new_item'          => __( 'Add New Testimonial', 'tropical_testimonials' ),
			'add_new'               => __( 'Add New', 'tropical_testimonials' ),
			'new_item'              => __( 'New testimonial', 'tropical_testimonials' ),
			'edit_item'             => __( 'Edit Testimonial', 'tropical_testimonials' ),
			'update_item'           => __( 'Update Testimonial', 'tropical_testimonials' ),
			'view_item'             => __( 'View Testimonial', 'tropical_testimonials' ),
			'view_items'            => __( 'View Testimonials', 'tropical_testimonials' ),
			'search_items'          => __( 'Search Testimonials', 'tropical_testimonials' ),
			'not_found'             => __( 'Not found', 'tropical_testimonials' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'tropical_testimonials' ),
			'featured_image'        => __( 'Featured Image', 'tropical_testimonials' ),
			'set_featured_image'    => __( 'Set featured image', 'tropical_testimonials' ),
			'remove_featured_image' => __( 'Remove featured image', 'tropical_testimonials' ),
			'use_featured_image'    => __( 'Use as featured image', 'tropical_testimonials' ),
			'insert_into_item'      => __( 'Insert into testimonials', 'tropical_testimonials' ),
			'uploaded_to_this_item' => __( 'Uploaded to this testimonial', 'tropical_testimonials' ),
			'items_list'            => __( 'Testimonials list', 'tropical_testimonials' ),
			'items_list_navigation' => __( 'Testimonials list navigation', 'tropical_testimonials' ),
			'filter_items_list'     => __( 'Filter testimonials list', 'tropical_testimonials' ),
		);
		$rewrite = array(
			'slug'                  => 'testimonials',
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => true,
		);
		$args = array(
			'label'                 => __( 'Testimonial', 'tropical_testimonials' ),
			'description'           => __( 'Testimonials', 'tropical_testimonials' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'comments', ),
			'taxonomies'            => array( 'category', 'post_tag' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-format-status',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => 'review',
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'rewrite'               => $rewrite,
			'capability_type'       => 'post',
			'show_in_rest'          => true,
		);
	register_post_type( 'testimonials', $args );
	}
	
}
