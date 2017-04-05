<?php
/**
 * Encryption functions to encrypt and decrypt values.
 *
 * @package           WPX
 *
 * @since             1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'wpx_encrypt' ) ) {

    /**
     * Encrypt an object using the AUTH_SALT from wp-config.php as the encryption key.
     *
     * @since  1.0.0
     *
     * @param  mixed $value The value to encrypt.
     *
     * @return string The encrypted base64-encoded string.
     */
    function wpx_encrypt( $value ) {
        // Check to make sure encryption (php-mcrypt) is enabled on the server
        if ( ! function_exists( 'mcrypt_get_iv_size' ) ) {
            // Set the error message
            $error_message = _x( 'Encryption requires the php-mcrypt module to encrypt the password. Please install php-mcrypt, restart your server, and reload the page.', 'Crypto', 'wpx' );

            // Write to error log
            error_log( $error_message );

            // Return the original value
            return $value;
        }

        // Set the encryption values
        $iv_size = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB );
        $iv      = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
        $h_key   = hash( 'sha256', AUTH_SALT, TRUE );

        // Get the encrypted result
        $encrypted = mcrypt_encrypt( MCRYPT_RIJNDAEL_256, $h_key, $value, MCRYPT_MODE_ECB, $iv );

        // Get the base 64 encoded string
        $base_64 = base64_encode( $encrypted );

        // Return the base 64 encoded string
        return $base_64;
    }

} // wpx_encrypt

/**
 * Filters a value through encryption and returns an encrypted string.
 *
 * @since  1.0.0
 *
 * @param  mixed $value The value to encrypt.
 *
 * @return string The encrypted base64-encoded string.
 */
add_filter( 'wpx_encrypt', 'wpx_encrypt' );

if ( ! function_exists( 'wpx_decrypt' ) ) {

    /**
     * Decrypt a string using the AUTH_SALT from wp-config.php as the decryption key.
     *
     * @since  1.0.0
     *
     * @param  string $string The encrypted string to decrypt.
     *
     * @return string The decrypted plain-text string.
     */
    function wpx_decrypt( $string ) {
        // Check to make sure encryption (php-mcrypt) is enabled on the server
        if ( ! function_exists( 'mcrypt_get_iv_size' ) ) {
            // Set the error message
            $error_message = _x( 'Decryption requires the php-mcrypt module to encrypt the password. Please install php-mcrypt, restart your server, and reload the page.', 'Crypto', 'wpx' );

            // Write to error log
            error_log( $error_message );

            // Return the original string
            return $string;
        }

        // Set the decryption values
        $iv_size = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB );
        $iv      = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
        $h_key   = hash( 'sha256', AUTH_SALT, TRUE );

        // Decode the string to descrypt
        $decoded = base64_decode( $string );

        // Decrypt the decoded string
        $decrypted = mcrypt_decrypt( MCRYPT_RIJNDAEL_256, $h_key, $decoded, MCRYPT_MODE_ECB, $iv );

        // Trim and return the decrypted string
        return trim( $decrypted );
    }

} // wpx_decrypt

/**
 * Filters a value through decryption and returns the decrypted string.
 *
 * @since  1.0.0
 *
 * @param  string $string The base64-encoded encrypted string to decrypt.
 *
 * @return string The decrypted plain-text string.
 */
add_filter( 'wpx_decrypt', 'wpx_decrypt' );