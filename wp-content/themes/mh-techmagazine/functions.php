<?php

/***** Fetch Theme Data *****/

$mh_magazine_lite_data = wp_get_theme('mh-magazine-lite');
$mh_magazine_lite_version = $mh_magazine_lite_data['Version'];
$mh_techmagazine_data = wp_get_theme('mh-techmagazine');
$mh_techmagazine_version = $mh_techmagazine_data['Version'];

/***** Load Stylesheets *****/

function mh_techmagazine_styles() {
	global $mh_magazine_lite_version, $mh_techmagazine_version;
    wp_enqueue_style('mh-magazine-lite', get_template_directory_uri() . '/style.css', array(), $mh_magazine_lite_version);
    wp_enqueue_style('mh-techmagazine', get_stylesheet_uri(), array('mh-magazine-lite'), $mh_techmagazine_version);
    if (is_rtl()) {
		wp_enqueue_style('mh-magazine-lite-rtl', get_template_directory_uri() . '/rtl.css', array(), $mh_magazine_lite_version);
	}
}
add_action('wp_enqueue_scripts', 'mh_techmagazine_styles');

/***** Load Translations *****/

function mh_techmagazine_theme_setup(){
	load_child_theme_textdomain('mh-techmagazine', get_stylesheet_directory() . '/languages');
}
add_action('after_setup_theme', 'mh_techmagazine_theme_setup');

/***** Change Defaults for Custom Colors *****/

function mh_techmagazine_custom_colors() {
	remove_theme_support('custom-header');
	add_theme_support('custom-header', array('default-image' => '', 'default-text-color' => '333f49', 'width' => 300, 'height' => 100, 'flex-width' => true, 'flex-height' => true));
}
add_action('after_setup_theme', 'mh_techmagazine_custom_colors');

/***** Remove Functions from Parent Theme *****/

function mh_techmagazine_remove_parent_functions() {
    remove_action('mh_before_header', 'mh_magazine_boxed_container_open');
    remove_action('mh_after_footer', 'mh_magazine_boxed_container_close');
}
add_action('wp_loaded', 'mh_techmagazine_remove_parent_functions');

/***** Enable Wide Layout *****/

function mh_techmagazine_wide_container_open() {
	echo '<div class="mh-container mh-container-outer">' . "\n";
}
add_action('mh_after_header', 'mh_techmagazine_wide_container_open');

function mh_techmagazine_wide_container_close() {
	mh_before_container_close();
	echo '</div><!-- .mh-container-outer -->' . "\n";
}
add_action('mh_before_footer', 'mh_techmagazine_wide_container_close');

?>