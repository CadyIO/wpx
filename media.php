<?php
/**
 * Media functions.
 *
 * @package           WPX
 *
 * @since             1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'wpx_remove_image_dimensions' ) ) {

    /**
     * Removes the height and width attributes from inserted images.
     *
     * @since 1.0.0
     *
     * @param string $html The image HTML string.
     *
     * @return string The resulting HTML.
     */
    function wpx_remove_image_dimensions( $html ) {
        return preg_replace( '/(width|height)="\d*"\s/', '', $html );
    }

} // wpx_remove_image_dimensions

if ( ! function_exists( 'wpx_register_remove_image_dimensions' ) ) {

    /**
     * Register remove the height and width attributes from inserted images.
     *
     * @since 1.0.0
     */
    function wpx_register_remove_image_dimensions() {
        add_filter( 'post_thumbnail_html', 'wpx_remove_image_dimensions', 10 );
        add_filter( 'image_send_to_editor', 'wpx_remove_image_dimensions', 10 );
    }

} // wpx_register_remove_image_dimensions