<?php
/**
 * Theme functions.
 *
 * @package           WPX
 *
 * @since             1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'wpx_is_blog' ) ) {

    /**
     * Check to see if the current page is the blog page.
     *
     * @since 1.0.0
     *
     * @return bool True if the current page is the blog page, false otherwise.
     */
    function wpx_is_blog() {
        return ( ( ! is_front_page() && is_home() ) || is_single() );
    }

} // wpx_is_blog

if ( ! function_exists( 'wpx_is_login_page' ) ) {

    /**
     * Check if the current page is the login page.
     *
     * @return bool True if current page is Login Page, false otherwise
     */
    function wpx_is_login_page() {
        return ( 'wp-login.php' === $_SERVER['REQUEST_URI'] );
    }

} // wpx_is_login_page

if ( ! function_exists( 'wpx_javascript_detection' ) ) {

    /**
     * Handles JavaScript detection.
     *
     * Adds a `js` class to the root `<html>` element when JavaScript is detected.
     *
     * @since 1.0.0
     */
    function wpx_javascript_detection() {
        echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
    }

} // wpx_javascript_detection

if ( ! function_exists( 'wpx_register_javascript_detection' ) ) {

    /**
     * Register JavaScript detection.
     *
     * Adds a `js` class to the root `<html>` element when JavaScript is detected.
     *
     * @since 1.0.0
     */
    function wpx_register_javascript_detection() {
        add_action( 'wp_head', 'wpx_javascript_detection', 0 );
    }

} // wpx_register_javascript_detection

if ( ! function_exists( 'wpx_cdn_jquery' ) ) {

    /**
    * Enqueues jQuery from CDN rather than core Wordpress include.
    *
    * @since 1.0.0
    */
    function wpx_cdn_jquery() {
        // Do not change jQuery in admin section
        if ( is_admin() ) {
            return;
        }

        // Get the current page
        global $pagenow;

        // Do not change jQuery on login page
        if( 'wp-login.php' === $pagenow ) {
            return;
        }

        // Remove the core jQuery
        wp_deregister_script( 'jquery' );

        // Enqueue jQuery from cdnjs
        wp_enqueue_script( 'jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js', array(), '3.1.1' );
    }

} // wpx_cdn_jquery

if ( ! function_exists( 'wpx_register_cdn_jquery' ) ) {

    /**
     * Registers jQuery from CDN.
     *
     * @since 1.0.0
     */
    function wpx_register_cdn_jquery() {
        add_action( 'init', 'wpx_cdn_jquery' );
    }

} // wpx_register_cdn_jquery