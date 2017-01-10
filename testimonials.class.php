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
		self::addShortCodes();
		self::getInjectedCode();
		load_plugin_textdomain(TROPICAL_TESTIMONIALS_TEXT_DOMAIN, false, TROPICAL_TESTIMONIALS_PLUGIN_DIR.'/translations/');	
	}
	
	public static function getInjectedCode(){
		add_action( 'wp_footer', array('Testimonials', 'addRichSnippet' ));
	}
	
	public static function addRichSnippet() {
		$rating = self::getTotalRating();
		$total = self::getAmountRatings();
	    echo "
	    <script type=\"application/ld+json\">
{
  \"@context\": \"http://schema.org/\",
  \"@type\": \"Organization\",
  \"name\": \"Clip4you\",
  \"url\": \"http://clip4you.nl\",
  \"aggregateRating\": {
    \"@type\": \"AggregateRating\",
    \"ratingValue\": \"{$rating}\",
    \"bestRating\": \"5\",
    \"worstRating\": \"1\",
    \"ratingCount\": \"$total\"
  }
}
</script>";
	}
	
	public static function getTotalRating(){
		$transientname = "testimonial_total";
		$transient = get_transient($transientname);
		if( ! empty( $transient ) ) {
	    	return $transient;
		} else {
			$transientData = self::generateTotalRating();
	    	set_transient( $transientname, $transientData, DAY_IN_SECONDS );
	    	return $transientData;
		}
	}
	
	private static function generateTotalRating(){
		$args = array(
			'posts_per_page'   => 100000,
			'offset'           => 0,
			'category'         => '',
			'category_name'    => '',
			'orderby'          => 'date',
			'order'            => 'DESC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'post',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'post_type'		   => 'testimonials',
			'author'	   => '',
			'author_name'	   => '',
			'post_status'      => 'publish',
			'suppress_filters' => true 
		);
		$posts_array = get_posts( $args );
		$count = 0;
		$total = 0;
		foreach ($posts_array as $post){
			$count ++;
			$total += (float)get_post_meta($post->ID, 'rating')[0];
		}
		return $total / $count;
	}
	
	public static function getAmountRatings(){
		$transientname = "testimonial_amount";
		$transient = get_transient($transientname);
		if( ! empty( $transient ) ) {
	    	return $transient;
		} else {
			$transientData = self::generateAmountRatings();
	    	set_transient( $transientname, $transientData, DAY_IN_SECONDS );
	    	return $transientData;
		}
	}
	
	private static function generateAmountRatings(){
		$args = array(
			'posts_per_page'   => 100000,
			'offset'           => 0,
			'category'         => '',
			'category_name'    => '',
			'orderby'          => 'date',
			'order'            => 'DESC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'post',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'post_type'		   => 'testimonials',
			'author'	   => '',
			'author_name'	   => '',
			'post_status'      => 'publish',
			'suppress_filters' => true 
		);
		$posts_array = get_posts( $args );
		$count = 0;
		foreach ($posts_array as $post){
			$count ++;
		}
		return $count;
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
									'testimonial_text' => $d['testimonial_text'])
        );
        wp_insert_post($new_post);
        setcookie("testimonial-set",'done',time()+31556926 ,'/');
		wp_redirect(home_url()."/testimonial/?a=y");
		exit();
	}
	
	private static function showForm(){
		if(isset($_COOKIE['testimonial-set'])) return true;
		if(isset($_GET['a'])) return true;
		return false;
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
		add_shortcode( 'testimonial-list', array('Testimonials', 'shortcodeTestimonialList') );
	}
	
	public static function shortcodeTestimonialInput(){
		wp_enqueue_script("RateYo");
		wp_enqueue_script("testimonial-js");
		wp_enqueue_style("RateYo");
		wp_enqueue_style("testimonial-css");
		if(self::showForm()) return self::getTemplatePart("testimonial-submitted");
		return self::getTemplatePart("testimonial", "form");
	}
	
	public static function shortcodeTestimonialList(){
		$output = '';
		$testimonials = self::getTestimonialsAsArray();
		foreach($testimonials as $testimonial){
			$output .= self::getTemplatePart("testimonial","overview-item", $testimonial);
		}
		return $output;
	}
	
	private static function getTestimonialsAsArray(){
		$output = array();
		$posts = get_posts(array(
		    'post_type'   => 'testimonials',
		    'post_status' => 'publish',
		    'posts_per_page' => -1,
		    'fields' => 'ids'
		    )
		);
		foreach($posts as $post){
			array_push($output, get_post_meta($post));
		}
		return $output;
	}
	
	public static function registerStylesScripts(){
		//styles
		wp_register_style("RateYo", TROPICAL_TESTIMONIALS_PLUGIN_URI."assets/css/jquery.rateyo.min.css", array(), "2.2.0");
		wp_register_style("testimonial-css", TROPICAL_TESTIMONIALS_PLUGIN_URI."assets/css/styles.css", array(), "1.0.0");
		
		//scripts
		wp_register_script("testimonial-js", TROPICAL_TESTIMONIALS_PLUGIN_URI."assets/js/app.js", array(), "1.0.0", true);
		wp_register_script("RateYo", TROPICAL_TESTIMONIALS_PLUGIN_URI."assets/js/jquery.rateyo.min.js", array('jquery', 'testimonial-js'), "2.2.0", true);
	}
	
	private static function getTemplatePart($slug, $name = null, $data = null){
		$templates = array();
	    $name = (string) $name;
	    if ( '' !== $name )
	        $templates[] = "{$slug}-{$name}.php";
	    $templates[] = "{$slug}.php";
	    $template = locate_template($templates, true, false);
		
		if( !$template ){
			if(file_exists(dirname(__FILE__) . "/templates/$templates[0]")){
				ob_start();
				include( dirname(__FILE__) . "/templates/$templates[0]" );
				return ob_get_clean();
			}elseif(file_exists(dirname(__FILE__) . "/templates/$templates[1]")){
				ob_start();
				include( dirname(__FILE__) . "/templates/$templates[1]" );
				return ob_get_clean();
			}else{
				return '';
			}
			
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
			'name'                  => _x( 'Testimonials', 'Post Type General Name', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'singular_name'         => _x( 'Testimonial', 'Post Type Singular Name', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'menu_name'             => __( 'Testimonials', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'name_admin_bar'        => __( 'Testimonials', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'archives'              => __( 'Testimonials', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'attributes'            => __( 'Testimonials Attributes', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'parent_item_colon'     => __( 'Parent testimonial:', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'all_items'             => __( 'All testimonials', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'add_new_item'          => __( 'Add New Testimonial', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'add_new'               => __( 'Add New', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'new_item'              => __( 'New testimonial', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'edit_item'             => __( 'Edit Testimonial', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'update_item'           => __( 'Update Testimonial', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'view_item'             => __( 'View Testimonial', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'view_items'            => __( 'View Testimonials', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'search_items'          => __( 'Search Testimonials', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'not_found'             => __( 'Not found', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'not_found_in_trash'    => __( 'Not found in Trash', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'featured_image'        => __( 'Featured Image', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'set_featured_image'    => __( 'Set featured image', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'remove_featured_image' => __( 'Remove featured image', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'use_featured_image'    => __( 'Use as featured image', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'insert_into_item'      => __( 'Insert into testimonials', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'uploaded_to_this_item' => __( 'Uploaded to this testimonial', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'items_list'            => __( 'Testimonials list', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'items_list_navigation' => __( 'Testimonials list navigation', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'filter_items_list'     => __( 'Filter testimonials list', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
		);
		$rewrite = array(
			'slug'                  => 'testimonials',
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => true,
		);
		$args = array(
			'label'                 => __( 'Testimonial', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
			'description'           => __( 'Testimonials', TROPICAL_TESTIMONIALS_TEXT_DOMAIN ),
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
