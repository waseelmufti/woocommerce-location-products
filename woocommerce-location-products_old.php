<?php 


/**
* Plugin Name: Woocommerce Location Products
Description: This plugin is filter out woocommerce products based on location -Custom product field
Version: 1.0.0
Author: Waseel Mufti
Author URI: localhost
License: GPLv2 or later
Text Domain: woocommerce-location-product
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
*/

/***Admin Side Code****/
/***************************/
/**************************/
/* 
* On Activation of the plugin
* adding location post type
* 
*/
function woocommerce_location_product_activate(){
	location_post_type();
	flush_rewrite_rules();
}

function woocommerce_location_product_deactivate(){
		flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'woocommerce_location_product_activate' );
register_deactivation_hook( __FILE__, 'woocommerce_location_product_deactivate' );

/*
* adding cutom post type: 
* location in the admin side
*/
function location_post_type(){

	$labels = array(
		'name' => 'Locations',
		'singular_name' => 'location',
		'add_new' => 'Add Location',
		'add_new_item' => 'Add New Location',
		'edit_item' => 'Edit Location',
	);

	$supports = array('title');

	register_post_type( 'location', array( 
		'public'=>false,
		'show_ui' =>true, 
		'labels'=> $labels,
		'supports' => $supports,
		 ) );
}

/* adding custom product field: location */ 
function add_custom_product_text_filed(){
	global $woocommerce, $post;

	$locatons = array('all' => 'All Locations');
	$locations_posts = get_posts(array('post_type' => 'location', 'numberposts' => -1));
	foreach ($locations_posts as $loc_post) {
		$locatons[$loc_post->ID] = $loc_post->post_title;
	}

	echo '<div class="options_group">';
	
	woocommerce_wp_select(
		array(
			'id' => '_location',
			'label' => __('Location'),
			'desc_tip' => true,
			'description'	=> __('Enter the product location here'),
			'options' => $locatons,
		)
	);
	echo '</div>';
}

function add_custom_product_text_fields_save($post_id){
	
	$_location = $_POST['_location'];
	if( !empty($_location) ){
		update_post_meta( $post_id, '_location', esc_attr( $_location ) );
	}

}


/*
* This is the function which is called and intialize
* other location post type
* adding location filed in the product woocommerce genral tab
******/
function add_location_product_custom_field(){
	if(in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option('active_plugins') ))) {
		location_post_type();
		//Show custom location product field
		add_action( 'woocommerce_product_options_general_product_data', 'add_custom_product_text_filed' );
		// Save Location Field Data
		add_action( 'woocommerce_process_product_meta', 'add_custom_product_text_fields_save' );
	}
}

/* When plugin is intialize */
add_action( 'init', 'add_location_product_custom_field' );


/**
 * Removes media buttons from post types.
 */
add_filter( 'wp_editor_settings', function( $settings ) {
    $current_screen = get_current_screen();

    // Post types for which the media buttons should be removed.
    $post_types = array( 'location' );

    // Bail out if media buttons should not be removed for the current post type.
    if ( ! $current_screen || ! in_array( $current_screen->post_type, $post_types, true ) ) {
        return $settings;
    }

    $settings['media_buttons'] = false;

    return $settings;
} );

/******End Admin Side Functionality***************/
/*Loads CSS and Js on frontend*/
function woocommerce_location_product_enqueue(){
	$rand = rand(1, 999999999);
	wp_enqueue_style( 'wp_style', plugins_url( '/assets/css/wp_style.css', __FILE__ ), '', $rand);
	wp_enqueue_script( 'wp_script', plugins_url( '/assets/js/wp_js.js', __FILE__ ),'' ,$rand ,true);
	wp_localize_script( 'wp_script', 'ajax_url', admin_url('admin-ajax.php') );
}
add_action( 'wp_enqueue_scripts', 'woocommerce_location_product_enqueue' );

//set default value of cookies 
function set_default_location_product(){
	if(!isset($_COOKIE['wc_location_product_id'])){
		setcookie('wc_location_product_id', 'dummy', (time()+60*60), '/');
	}
}
add_action( 'init', 'set_default_location_product' );
/***Add Switch Location Button****/
function switch_location_button($nav, $args){
	if ($args->theme_location == 'main-navigation' OR $args->theme_location =='mobile-navigation') {
		$selected = '';
		$locations_posts = get_posts(array('post_type' => 'location', 'numberposts' => -1));
		//Dropdown menu in the navigation bar
		$nav .= '<li id="nav-menu-item-0007" class="menu-item menu-item-type-post_type menu-item-object-page current-menu-item page_item page-item-1000 current_page_item eltdf-active-item narrow">';
		$nav .= '<select id="location_switcher" class="wpcf7-form-control wpcf7-select wlp-select-control location_switcher" style="margin-top:10px;">';
		/**
		if all location is selected all locations is selected in dropdown menu
		**/
		if (isset($_COOKIE['wc_location_product_id']) && !empty($_COOKIE['wc_location_product_id']) && !is_null($_COOKIE['wc_location_product_id'])) {
			if ($_COOKIE['wc_location_product_id'] == 'all') {
				$selected = 'selected';
			}
		}
		$nav .= '<option value="all" '.$selected.'>All Locations</option>';
		foreach ($locations_posts as $loc_post) {
			$selected = '';
			if (isset($_COOKIE['wc_location_product_id']) && !empty($_COOKIE['wc_location_product_id']) && !is_null($_COOKIE['wc_location_product_id'])) {
				// Show current location is selected in dropdown menu
				if ($_COOKIE['wc_location_product_id'] == $loc_post->ID) {
					$selected = 'selected';
				}
			}
			$nav .= '<option value="'.$loc_post->ID.'" '.$selected.'>'. __($loc_post->post_title).'</option>';
		}
		$nav .= '</select>';
		$nav .= '</li>';
		 
	}
    return $nav;
}
add_filter( 'wp_nav_menu_items', 'switch_location_button', 10, 2 ); 
/*Modal Box*/
function modal_box(){
	$locatons = array('all' => 'All Locations');
	$locations_posts = get_posts(array('post_type' => 'location', 'numberposts' => -1));
	if(!isset($_COOKIE['wc_location_product_id']) || (!empty($_COOKIE['wc_location_product_id']) && !is_null($_COOKIE['wc_location_product_id']) && $_COOKIE['wc_location_product_id'] == 'dummy')){
		if(!(is_page(['my-account', 'track-your-order', 'blog', 'contact-us', 'our-company', 'our-team']) || is_blog_page())) {
			require_once plugin_dir_path( __FILE__ ).'templates/popup.php';
			}
 	}
}

add_action( 'wp_footer', 'modal_box' );

/*Get Location Based Products*/
add_action( 'woocommerce_product_query', 'location_products' );
function location_products($q){
	if (isset($_COOKIE['wc_location_product_id']) && !empty($_COOKIE['wc_location_product_id']) && !is_null($_COOKIE['wc_location_product_id']) && $_COOKIE['wc_location_product_id'] != 'dummy') {
		$meta_query = $q->get('meta_query');
		$meta_query[] = array(
			'key' => '_location',
			'value' => $_COOKIE['wc_location_product_id'],
			'compare' => '=',
		);
		$q->set('meta_query', $meta_query);
	}
}

/* Filter Products woocommerce shortcodes*/
add_filter( 'woocommerce_shortcode_products_query', 'shortcode_products_query_on_location', 10, 3 );
function shortcode_products_query_on_location( $query_args, $atts, $loop_name ){
    if (isset($_COOKIE['wc_location_product_id']) && !empty($_COOKIE['wc_location_product_id']) && !is_null($_COOKIE['wc_location_product_id']) && $_COOKIE['wc_location_product_id'] != 'dummy') {

        $query_args['meta_query'] = array( array(
            'key'     => '_location',
            'value'   => $_COOKIE['wc_location_product_id'],
            'compare' => '=',
        ) );
    }
    return $query_args;
}
//Get Location Based Related Products
//not  completed
function get_related_products_007(){
	$get_related_products = 'all';
	if (isset($_COOKIE['wc_location_product_id']) && !empty($_COOKIE['wc_location_product_id']) && !is_null($_COOKIE['wc_location_product_id']) && $_COOKIE['wc_location_product_id'] != 'dummy') {
		global $post;
	$related = get_post_meta( $post->ID, '_location', true );
	if ($related) { // remove category based filtering
		$args['post__in'] = $related;
	}
	elseif (get_option( 'crp_empty_behavior' ) == 'none') { // don't show any products
		$args['post__in'] = array(0);
	}

	return $args;
}
}
 add_filter( 'woocommerce_related_products_args', 
                'get_related_products_007' );

/*AJAX request handeler*/
function wc_location_product_ajax(){
	// if(session_status() == PHP_SESSION_NONE ){
	// 	session_start();
	// 	$_SESSION['hello'] = 'world';
	// }
	//print_r($_COOKIE);exit;
 if(isset($_POST['location_id']) && !empty($_POST['location_id']) && !is_null($_POST['location_id'])){
 	setcookie('wc_location_product_id', $_POST['location_id'], (time()+60*60), '/');
 	echo 1;
 }
 exit();
}
add_action( 'wp_ajax_wc_location_product', 'wc_location_product_ajax' );
add_action( 'wp_ajax_nopriv_wc_location_product', 'wc_location_product_ajax' );
/** Helper Functions **/
/* This function checks that is the current page is a blog post*/
function is_blog_page(){
	global $post;

	$post_type = get_post_type($post);

	return ((is_home() || is_archive() || is_single()) && ($post_type == 'post'));
}