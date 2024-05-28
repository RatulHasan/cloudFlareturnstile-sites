<?php
/**
 * Plugin Name:         CloudFlare Turnstile Sites
 * Plugin URI:          https://github.com/RatulHasan/cloudFlare-Turnstile.git
 * Description:         This plugin will help you to block your site from unwanted visitors.
 * Version:             1.0.0
 * Requires PHP:        7.4
 * Requires at least:   5.6
 * Tested up to:        6.5
 * Author:              Ratul Hasan
 * Author URI:          https://ratuljh.wordpress.com/
 * License:             GPL-2.0-or-later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         cloudflare-turnstile
 * Domain Path:         /languages
 *
 * @package WordPress
 */

// To prevent direct access, if not define WordPress ABSOLUTE PATH then exit.
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

// Define the plugin version.
define( 'CFTS_VERSION', '1.0.0' );
function cloudflare_key() {
    $sitekey   = '0x4AAAAAAAbVop3pyZX0TV-M';
    $secretkey = '0x4AAAAAAAbVohfYIG2ycy0tq_N_6Nd6v8U';

    return [ $sitekey, $secretkey ];
}

add_action( 'wp_head', function () {
    wp_enqueue_script( 'cloudflare-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js' );
} );


/*
 * Adding Cloudflare Turnstile to Login Form by wpcookie
 * https://redpishi.com/wordpress-tutorials/cloudflare-turnstile-captcha-wordpress/
 */
function login_style() {
    wp_register_script( 'login-recaptcha', 'https://challenges.cloudflare.com/turnstile/v0/api.js', false, null );
    wp_enqueue_script( 'login-recaptcha' );
    echo '<style>p.submit, p.forgetmenot {margin-top: 10px!important;}.login form{width: 303px;} div#login_error {width: 322px;}</style>';
}

add_action( 'login_enqueue_scripts', 'login_style' );

add_action( 'login_form', function () {
    echo '<div class="cf-turnstile" data-sitekey="' . cloudflare_key()[0] . '"></div>';
} );

add_action( 'wp_authenticate_user', function ( $user, $password ) {
    $captcha = $_POST['cf-turnstile-response'];
    if ( ! $captcha ) {
        return new WP_Error( 'Captcha Invalid', __( '<center>Captcha Invalid! Please check the captcha!</center>' ) );
        die();
        exit;
    }
    $secretKey = cloudflare_key()[1];
    $ip        = $_SERVER['REMOTE_ADDR'];

    $url_path = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    $data     = [ 'secret' => $secretKey, 'response' => $captcha, 'remoteip' => $ip ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'content' => http_build_query( $data )
        ]
    ];

    $stream = stream_context_create( $options );

    $result = file_get_contents(
        $url_path, false, $stream );

    $response = $result;

    $responseKeys = json_decode( $response, true );
    if ( intval( $responseKeys['success'] ) !== 1 ) {
        return new WP_Error( 'Captcha Invalid', __( '<center>Captcha Invalid! Please check the captcha!</center>' ) );
        die();
        exit;
    } else {
        return $user;
    }
}, 10, 2 );


/*
 * Adding Cloudflare Turnstile to WordPress Comment
 * https://redpishi.com/wordpress-tutorials/cloudflare-turnstile-captcha-wordpress/
 */
function is_valid_captcha( $captcha ) {

    if ( ! $captcha ) {
        return false;
    }
    $secretKey = cloudflare_key()[1];
    $ip        = $_SERVER['REMOTE_ADDR'];

    $url_path = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    $data     = [ 'secret' => $secretKey, 'response' => $captcha, 'remoteip' => $ip ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'content' => http_build_query( $data )
        ]
    ];

    $stream = stream_context_create( $options );

    $result = file_get_contents(
        $url_path, false, $stream );

    $response = $result;

    $responseKeys = json_decode( $response, true );
    if ( intval( $responseKeys['success'] ) !== 1 ) {
        return false;
    } else {
        return true;
    }
}

add_action( 'init', function () {
    if ( ! is_user_logged_in() ) {
        add_action( 'pre_comment_on_post', function () {
            $recaptcha = $_POST['cf-turnstile-response'];
            if ( empty( $recaptcha ) ) {
                wp_die( __( "<b>ERROR:</b> please select <b>I'm not a robot!</b><p><a href='javascript:history.back()'>« Back</a></p>" ) );
            } elseif ( ! is_valid_captcha( $recaptcha ) ) {
                wp_die( __( "<b>please select I'm not a robot!</b>" ) );
            }
        } );

        add_filter( 'comment_form_defaults', function ( $submit_field ) {

            $submit_field['submit_field'] = '<div class="cf-turnstile" data-sitekey="' . cloudflare_key()[0] . '"></div><br>' . $submit_field['submit_field'];

            return $submit_field;
        } );
    }
} );
