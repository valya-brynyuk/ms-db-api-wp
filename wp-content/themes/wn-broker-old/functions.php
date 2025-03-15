<?php

/*----------------------------------------------------------------------------------*\
	THEME TITLE TAG SUPPORT
\*----------------------------------------------------------------------------------*/

add_theme_support( 'title-tag' );

/*------------------------------------------------------------------------*\
	STYLES AND SCRIPTS
\*------------------------------------------------------------------------*/

// Load the theme stylesheets
function theme_styles()  { 
	wp_enqueue_style( 'main', get_template_directory_uri() . '/style.css' );
}
add_action('wp_enqueue_scripts', 'theme_styles');

// Load the theme scripts
function theme_scripts() {
	wp_register_script('googlemaps_js','httpS://maps.googleapis.com/maps/api/js?key=APIKEYISHERE', array('jquery'),'1.1', true);
	wp_register_script('functions_js', get_template_directory_uri().'/dist/all.min.js', array('jquery'),'1.1', true);

	wp_enqueue_script('googlemaps_js');
	wp_enqueue_script('functions_js');
}
add_action( 'wp_enqueue_scripts', 'theme_scripts' );

/*------------------------------------------------------------------------*\
	HIDE ADMINBAR (for viewing sticky header when logged in)
\*------------------------------------------------------------------------*/

add_filter('show_admin_bar', '__return_false');

/*------------------------------------------------------------------------*\
	REGISTER MENUS
\*------------------------------------------------------------------------*/

function register_my_menus() {
	register_nav_menus(
		array(
			'main-nav' => __('Main Nav')
		)
	);
}
add_action( 'init', 'register_my_menus' );

/*------------------------------------------------------------------------*\
	REGISTER POST TYPE
\*------------------------------------------------------------------------*/

// add_action( 'init', 'create_post_type' );
// function create_post_type() {
//   register_post_type( 'event',
// 	array(
// 	  'labels' => array(
// 		'name' => __( 'Events' ),
// 		'singular_name' => __( 'Event' ),
// 		'add_new_item' => __( 'Add New Event' )
// 	),
// 	'menu_icon' => 'dashicons-calendar',
// 	'public' => true,
// 	'has_archive' => true,
// 	'hierarchical' => true,
// 	'supports' => array( 'page-attributes','title','editor','revisions')
// 	)
// 	);
// }

/*------------------------------------------------------------------------*\
	OPTIONS PAGE
\*------------------------------------------------------------------------*/

if( function_exists('acf_add_options_page') ) {
	acf_add_options_page();
}

/*------------------------------------------------------------------------*\
	CUSTOM IMAGE SIZES
\*------------------------------------------------------------------------*/

//thumbnail 150x150 (cropped)
//medium 300 300
//large 1024 1024
// medium_large (768w, no height (responsive) - already exists behind scenes in wordpress 4.4)
add_image_size( 'responsive_large', 1600 );
add_image_size( 'responsive_xlarge', 2000 );

//update_option( 'thumbnail_size_w', 160 );
//update_option( 'thumbnail_size_h', 160 );
//update_option( 'thumbnail_crop', 1 );