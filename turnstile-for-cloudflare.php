<?php
/**
 * Plugin Name:         Turnstile for CloudFlare
 * Plugin URI:          https://github.com/RatulHasan/turnstile-for-cloudflare
 * Description:         This plugin will help you to block your site from unwanted visitors.
 * Version:             1.0.0
 * Requires PHP:        7.4
 * Requires at least:   5.6
 * Tested up to:        6.5
 * Author:              Ratul Hasan
 * Author URI:          https://ratuljh.wordpress.com/
 * License:             GPL-2.0-or-later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         turnstile-for-cloudflare
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
    return [ get_option( 'cloudflare_site_key', '' ), get_option( 'cloudflare_secret_key', '' ) ];
}

// Add settings link on plugin page
function cloudflare_turnstile_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=turnstile-for-cloudflare">' . __( 'Settings', 'turnstile-for-cloudflare' ) . '</a>';
    array_unshift( $links, $settings_link );

    return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'cloudflare_turnstile_settings_link' );

// After activation redirect to settings page
function cloudflare_turnstile_redirect() {
	add_option( 'cloudflare_turnstile_redirect', true );
	add_option( 'cloudflare_turnstile_version', CFTS_VERSION );
}

register_activation_hook( __FILE__, 'cloudflare_turnstile_redirect' );

add_action( 'admin_init', function () {
	if ( get_option( 'cloudflare_turnstile_redirect', false ) ) {
		delete_option( 'cloudflare_turnstile_redirect' );
		wp_redirect( admin_url( 'options-general.php?page=turnstile-for-cloudflare' ) );
	}
} );

// Add menu in admin panel
add_action( 'admin_menu', function () {
    add_options_page( 'Turnstile for Cloudflare', 'Turnstile for Cloudflare', 'manage_options', 'turnstile-for-cloudflare', 'cloudflare_turnstile_page' );
} );

// Register settings
function cloudflare_turnstile_register_settings() {
    register_setting( 'cloudflare_turnstile', 'cloudflare_turnstile_enable' );
    register_setting( 'cloudflare_turnstile', 'cloudflare_site_key' );
    register_setting( 'cloudflare_turnstile', 'cloudflare_secret_key' );
    register_setting( 'cloudflare_turnstile', 'cloudflare_turnstile_login_enable' );
    register_setting( 'cloudflare_turnstile', 'cloudflare_turnstile_comment_enable' );
}

add_action( 'admin_init', 'cloudflare_turnstile_register_settings' );

function cloudflare_turnstile_page() {
    ?>
	<div class="wrap">
        <h2><?php
            esc_html_e( 'Turnstile for Cloudflare', 'turnstile-for-cloudflare' ); ?></h2>
        <small>
            <?php
            /* translators: %1$s: Opening anchor tag, %2$s: Closing anchor tag */
            echo sprintf( esc_html__( 'Enter your Cloudflare Site Key and Secret Key to enable Turnstile for Cloudflare. Or create one from %1$sCloudflare%2$s.', 'turnstile-for-cloudflare' ), '<a href="https://dash.cloudflare.com/sign-up?to=/:account/turnstile" target="_blank">', '</a>' );
            ?>
        </small>
        <form
	        method="post"
	        action="options.php"
        >
            <?php
            settings_fields( 'cloudflare_turnstile' );
            do_settings_sections( 'cloudflare_turnstile' );
            $sitekey   = get_option( 'cloudflare_site_key' );
            $secretkey = get_option( 'cloudflare_secret_key' );
            ?>
	        <table class="form-table form-table-wide">
                <tr>
                    <th scope="row"><label for="cloudflare_turnstile_enable"><?php
                            esc_html_e( 'Enable Turnstile for Cloudflare', 'turnstile-for-cloudflare' ); ?></label></th>
                    <td><input
		                    type="checkbox"
		                    id="cloudflare_turnstile_enable"
		                    name="cloudflare_turnstile_enable"
		                    value="1" <?php
                        checked( 1, get_option( 'cloudflare_turnstile_enable' ), true ); ?>></td>
                </tr>
                <tr>
                    <th scope="row"><label for="cloudflare_site_key"><?php
                            esc_html_e( 'Site Key', 'turnstile-for-cloudflare' ); ?></label></th>
                    <td><input
		                    type="text"
		                    id="cloudflare_site_key"
		                    name="cloudflare_site_key"
		                    value="<?php
                            echo esc_attr( $sitekey ); ?>"
		                    class="regular-text"
	                    ></td>
                </tr>
                <tr>
                    <th scope="row"><label for="cloudflare_secret_key"><?php
                            esc_html_e( 'Secret Key', 'turnstile-for-cloudflare' ); ?></label></th>
                    <td><input
		                    type="text"
		                    id="cloudflare_secret_key"
		                    name="cloudflare_secret_key"
		                    value="<?php
                            echo esc_attr( $secretkey ); ?>"
		                    class="regular-text"
	                    ></td>
                </tr>
                <tr>
                    <th scope="row"><label for="cloudflare_turnstile_login_enable"><?php
                            esc_html_e( 'Enable in Login Form', 'turnstile-for-cloudflare' ); ?></label></th>
                    <td><input
		                    type="checkbox"
		                    id="cloudflare_turnstile_login_enable"
		                    name="cloudflare_turnstile_login_enable"
		                    value="1" <?php
                        checked( 1, get_option( 'cloudflare_turnstile_login_enable' ), true ); ?>></td>
                </tr>
                <tr>
                    <th scope="row"><label for="cloudflare_turnstile_comment_enable"><?php
                            esc_html_e( 'Enable in Comment Form', 'turnstile-for-cloudflare' ); ?></label></th>
                    <td><input
		                    type="checkbox"
		                    id="cloudflare_turnstile_comment_enable"
		                    name="cloudflare_turnstile_comment_enable"
		                    value="1" <?php
                        checked( 1, get_option( 'cloudflare_turnstile_comment_enable' ), true ); ?>></td>
                </tr>
            </table>
            <?php
            submit_button(); ?>
        </form>
    </div>
    <?php
}

add_action( 'wp_head', function () {
    $enable = get_option( 'cloudflare_turnstile_enable', false );
    if ( ! $enable ) {
        return;
    }

    wp_enqueue_script( 'turnstile-for-cloudflare', 'https://challenges.cloudflare.com/turnstile/v0/api.js', [], CFTS_VERSION, true );
} );

// Adding Cloudflare Turnstile to Login Form
function login_style() {
    $enable = get_option( 'cloudflare_turnstile_enable', false );
    if ( ! $enable ) {
        return;
    }
    wp_register_script( 'login-recaptcha', 'https://challenges.cloudflare.com/turnstile/v0/api.js', false, CFTS_VERSION, true );
    wp_enqueue_script( 'login-recaptcha' );
    echo '<style>p.submit, p.forgetmenot {margin-top: 10px!important;}.login form{width: 303px;} div#login_error {width: 322px;}</style>';
}

add_action( 'login_enqueue_scripts', 'login_style' );

add_action( 'login_form', function () {
    $enable      = get_option( 'cloudflare_turnstile_enable', false );
    $enableLogin = get_option( 'cloudflare_turnstile_login_enable', false );
    if ( ! $enable || ! $enableLogin ) {
        return;
    }

    echo '<div class="cf-turnstile" data-sitekey="' . esc_attr( cloudflare_key()[0] ) . '"></div>';
} );

add_action( 'wp_authenticate_user', function ( $user, $password ) {
    $enable      = get_option( 'cloudflare_turnstile_enable', false );
    $enableLogin = get_option( 'cloudflare_turnstile_login_enable', false );
    if ( ! $enable || ! $enableLogin ) {
        return $user;
    }
    $captcha = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( $_POST['cf-turnstile-response'] ) : '';
    if ( ! $captcha ) {
        /* translators: %1$s: Opening center tag, %2$s: Closing center tag */
        return new WP_Error( 'Captcha Invalid', sprintf( __( '%1$sCaptcha Invalid! Please check the captcha!%2$s', 'turnstile-for-cloudflare' ), '<center>', '</center>' ) );
    }
    $responseKeys = getResponse_keys( $captcha );
    if ( intval( $responseKeys['success'] ) !== 1 ) {
        /* translators: %1$s: Opening center tag, %2$s: Closing center tag */
        return new WP_Error( 'Captcha Invalid', sprintf( __( '%1$sCaptcha Invalid! Please check the captcha!%2$s', 'turnstile-for-cloudflare' ), '<center>', '</center>' ) );
    } else {
        return $user;
    }
}, 10, 2 );

// Adding Cloudflare Turnstile to WordPress Comment
function is_valid_captcha( $captcha ) {
    if ( ! $captcha ) {
        return false;
    }
    $responseKeys = getResponse_keys( $captcha );

    return intval( $responseKeys['success'] ) === 1;
}

/**
 * getResponse_keys.
 *
 * @since 1.0.0
 *
 * @param string $captcha
 *
 * @return mixed
 */
function getResponse_keys( $captcha ) {
    $secretKey = cloudflare_key()[1];
    $ip        = $_SERVER['REMOTE_ADDR'];

    $url_path = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    $data     = [
        'secret'   => $secretKey,
        'response' => $captcha,
        'remoteip' => $ip,
    ];

    $response = wp_remote_post( $url_path, [
        'body'    => $data,
        'headers' => [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ],
    ] );

    if ( is_wp_error( $response ) ) {
        // Handle error
        $error_message = $response->get_error_message();
        echo 'Something went wrong: ' . esc_html( $error_message );
    } else {
        $result = wp_remote_retrieve_body( $response );

        return json_decode( $result, true );
    }
}

add_action( 'init', function () {
    $enable        = get_option( 'cloudflare_turnstile_enable', false );
    $enableComment = get_option( 'cloudflare_turnstile_comment_enable', false );
    if ( ! $enable || ! $enableComment || is_user_logged_in() ) {
        return;
    }

    // Enqueue the Turnstile script
    add_action( 'wp_enqueue_scripts', function () {
        wp_enqueue_script( 'turnstile-for-cloudflare', 'https://challenges.cloudflare.com/turnstile/v0/api.js', [], CFTS_VERSION, true );
    } );

    // Add the Turnstile widget to the comment form
    add_filter( 'comment_form_defaults', function ( $defaults ) {
        $sitekey                         = cloudflare_key()[0];
        $defaults['comment_notes_after'] .= '<div class="cf-turnstile" data-sitekey="' . esc_attr( $sitekey ) . '"></div>';

        return $defaults;
    } );

    // Verify the Turnstile response on comment submission
    add_action( 'pre_comment_on_post', function () {
        if ( ! isset( $_POST['cf-turnstile-response'] ) ) {
            wp_die( sprintf(
            /* translators: 1: Error message, 2: Back link */
                esc_html__( '<b>%1$s</b> please select I\'m not a robot! <p><a href="javascript:history.back()">« %2$s</a></p>', 'turnstile-for-cloudflare' ),
                esc_html__( 'ERROR:', 'turnstile-for-cloudflare' ),
                esc_html__( 'Back', 'turnstile-for-cloudflare' )
            ) );
        }

        $recaptcha = sanitize_text_field( $_POST['cf-turnstile-response'] );
        if ( empty( $recaptcha ) ) {
            wp_die( sprintf(
            /* translators: 1: Error message, 2: Back link */
                esc_html__( '<b>%1$s</b> please select I\'m not a robot! <p><a href="javascript:history.back()">« %2$s</a></p>', 'turnstile-for-cloudflare' ),
                esc_html__( 'ERROR:', 'turnstile-for-cloudflare' ),
                esc_html__( 'Back', 'turnstile-for-cloudflare' )
            ) );
        } elseif ( ! is_valid_captcha( $recaptcha ) ) {
            wp_die( sprintf(
            /* translators: %s: Error message */
                esc_html__( '<b>%s</b> please select I\'m not a robot!', 'turnstile-for-cloudflare' ),
                esc_html__( 'ERROR:', 'turnstile-for-cloudflare' )
            ) );
        }
    } );
} );
?>
