<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
class Testimonials {
	private static $initiated = false;
	
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
		self::registerPostTypes();
		self::addShortCodes();
		load_plugin_textdomain(TROPICAL_TESTIMONIALS_TEXT_DOMAIN, false, TROPICAL_TESTIMONIALS_PLUGIN_DIR.'/translations/');	
	}
	
	public static function addShortCodes(){
		add_shortcode( 'testimonial', array('Testimonials', 'shortcodeTestimonialInput') );
	}
	
	public static function shortcodeTestimonialInput(){
		return self::getTemplatePart("testimonial-form");
	}
	
	private static function getTemplatePart($slug, $name = null){
		$templates = array();
	    $name = (string) $name;
	    if ( '' !== $name )
	        $templates[] = "{$slug}-{$name}.php";
	    $templates[] = "{$slug}.php";
	    $template = locate_template($templates, true, false);
		if( !$template ){
			if(!file_exists(dirname(__FILE__) . "/templates/$templates[0]"));
			    return;
			    
			ob_start();
			include( dirname(__FILE__) . "/templates/$templates[0]" );
			return ob_get_clean();
		}
		if( $template ){
			if(!file_exists($template));
			    return;
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
			'slug'                  => 'testimonial',
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
