<?php
/*
Plugin Name: Search Box
Plugin URI: https://www.hoosoft.com/plugins/search-box/
Description: Animated Search form with Pure CSS3, replace search form with custom CSS styles.
Author: Jimmy Quan
Version: 1.0.1
Author URI: https://www.hoosoft.com
Text Domain: search-box
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
define( 'SEARCH_BOX_PREFIX', 'search_box' );

require_once('inc/options.php');
// register scripts on initialization
add_action('init', 'search_box_register_script');
function search_box_register_script() {
    wp_register_style( 'search-box-style', plugins_url('assets/css/style.css', __FILE__), false, '1.0.0', 'all');
}

// use the registered scripts above
add_action('wp_enqueue_scripts', 'search_box_enqueue_style');
function search_box_enqueue_style(){
   wp_enqueue_style( 'search-box-style' );

    $icon_color = get_option(SEARCH_BOX_PREFIX."_icon_color");
    $width = get_option(SEARCH_BOX_PREFIX."_width");
    $hover_width = get_option(SEARCH_BOX_PREFIX."_hover_width");

    $custom_css = $icon_color ? "#search-box-wrap #search-box-searchform input[type='submit'] {background-color:".sanitize_hex_color($icon_color).";}" : "";
    $custom_css .= $width ? "#search-box-searchform input[type='text'] {width:".esc_attr($width).";}" : "";
    $custom_css .= $hover_width ? '#search-box-searchform input[type="text"]:hover, #search-box-searchform input[type="text"]:focus { width:'.esc_attr($hover_width).'; }':'';
    if($custom_css) wp_add_inline_style( 'search-box-style', $custom_css );
}

add_action( 'admin_enqueue_scripts', 'search_box_enqueue_script_admin' );
function search_box_enqueue_script_admin( $hook_suffix ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'search-box-admin', plugins_url('assets/js/admin.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}

add_filter('get_search_form', 'search_box_get_search_form', 10, 2);
function search_box_get_search_form($form, $args){

    $format = current_theme_supports( 'html5', 'search-form' ) ? 'html5' : 'xhtml';
    if ( $args['aria_label'] ) {
        $aria_label = 'aria-label="' . esc_attr( $args['aria_label'] ) . '" ';
    } else {
        $aria_label = '';
    }

    if ( 'html5' === $format ) {
        $form = '<div class="style_1" id="search-box-wrap"><form role="search" ' . $aria_label . 'method="get" id="search-box-searchform" action="' . esc_url( home_url( '/' ) ) . '">
        <fieldset>
        <input id="s" name="s" type="text" placeholder="' . esc_attr_x( 'Search &hellip;', 'search-box' ) . '" value="' . get_search_query() . '" class="text_input" />
        <input name="submit" type="submit"  value="" />
        </fieldset>
    </form></div>';

    }else{
        $form = '<div class="style_1" id="search-box-wrap"><form role="search" ' . $aria_label . 'method="get" id="search-box-searchform" action="' . esc_url( home_url( '/' ) ) . '">
            <fieldset>
            <label class="screen-reader-text" for="s">' . __( 'Search for:', 'search-box' ) . '</label>
            <input id="s" name="s" type="text" value="' . get_search_query() . '" class="text_input" />
            <input name="submit" type="submit" value="" />
            </fieldset>
        </form></div>';
    }
    return $form;
}

// Search box shortcode
function search_box_shortcode() {
	return get_search_form(false);
}
add_shortcode('search_box', 'search_box_shortcode');
add_shortcode('search-box', 'search_box_shortcode');