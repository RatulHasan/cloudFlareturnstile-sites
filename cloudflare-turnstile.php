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
    return [ get_option( 'cloudflare_site_key', '' ), get_option( 'cloudflare_secret_key', '' ) ];
}

// Add settings link on plugin page
function cloudflare_turnstile_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=cloudflare-turnstile">Settings</a>';
    array_unshift( $links, $settings_link );

    return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'cloudflare_turnstile_settings_link' );

// Add menu in admin panel
add_action( 'admin_menu', function () {
    add_options_page( 'Cloudflare Turnstile', 'Cloudflare Turnstile', 'manage_options', 'cloudflare-turnstile', 'cloudflare_turnstile_page' );
} );

// Register settings
function cloudflare_turnstile_register_settings() {
	register_setting( 'cloudflare_turnstile', 'cloudflare_turnstile_enable' );
    register_setting( 'cloudflare_turnstile', 'cloudflare_site_key' );
    register_setting( 'cloudflare_turnstile', 'cloudflare_secret_key' );
}

add_action( 'admin_init', 'cloudflare_turnstile_register_settings' );

function cloudflare_turnstile_page() {
    ?>
    <div class="wrap">
        <h2>Cloudflare Turnstile</h2>
        <small>
            Enter your Cloudflare Site Key and Secret Key to enable Cloudflare Turnstile.
            Or create one from <a href="https://dash.cloudflare.com/sign-up?to=/:account/turnstile" target="_blank">Cloudflare</a>
        </small>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'cloudflare_turnstile' );
            do_settings_sections( 'cloudflare_turnstile' );
            // Create site key and secret key input field to save in database
            $sitekey   = get_option( 'cloudflare_site_key' );
            $secretkey = get_option( 'cloudflare_secret_key' );

            ?>
            <table class="form-table form-table-wide">
                <tr>
                    <th scope="row"><label for="cloudflare_turnstile_enable">Enable Cloudflare Turnstile</label></th>
	                <td>
		                <input type="checkbox" id="cloudflare_turnstile_enable" name="cloudflare_turnstile_enable" value="1" <?php checked( 1, get_option( 'cloudflare_turnstile_enable' ), true ); ?>>
	                </td>
                </tr>
                <tr>
                    <th scope="row"><label for="cloudflare_site_key">Site Key</label></th>
                    <td><input type="text" id="cloudflare_site_key" name="cloudflare_site_key" value="<?php echo $sitekey; ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="cloudflare_secret_key">Secret Key</label></th>
                    <td><input type="text" id="cloudflare_secret_key" name="cloudflare_secret_key" value="<?php echo $secretkey; ?>" class="regular-text"></td>
                </tr>
            </table>
            <?php
            submit_button();
            ?>
        </form>
    </div>
    <?php
}


add_action( 'wp_head', function () {
	$enable = get_option( 'cloudflare_turnstile_enable', false);
	if ( ! $enable ) {
		return;
    }

    wp_enqueue_script( 'cloudflare-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js' );
} );


/*
 * Adding Cloudflare Turnstile to Login Form by wpcookie
 */
function login_style() {
    $enable = get_option( 'cloudflare_turnstile_enable', false);
    if ( ! $enable ) {
        return;
    }
    wp_register_script( 'login-recaptcha', 'https://challenges.cloudflare.com/turnstile/v0/api.js', false, null );
    wp_enqueue_script( 'login-recaptcha' );
    echo '<style>p.submit, p.forgetmenot {margin-top: 10px!important;}.login form{width: 303px;} div#login_error {width: 322px;}</style>';
}

add_action( 'login_enqueue_scripts', 'login_style' );

add_action( 'login_form', function () {
    $enable = get_option( 'cloudflare_turnstile_enable', false);
    if ( ! $enable ) {
        return;
    }

    echo '<div class="cf-turnstile" data-sitekey="' . cloudflare_key()[0] . '"></div>';
} );

add_action( 'wp_authenticate_user', function ( $user, $password ) {
    $enable = get_option( 'cloudflare_turnstile_enable', false);
    if ( ! $enable ) {
        return $user;
    }
    $captcha = $_POST['cf-turnstile-response'];
    if ( ! $captcha ) {
        return new WP_Error( 'Captcha Invalid', __( '<center>Captcha Invalid! Please check the captcha!</center>' ) );
        die();
        exit;
    }
    $responseKeys = getResponse_keys( $captcha );
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
 */
function is_valid_captcha( $captcha ) {

    if ( ! $captcha ) {
        return false;
    }
    $responseKeys = getResponse_keys( $captcha );
    if ( intval( $responseKeys['success'] ) !== 1 ) {
        return false;
    } else {
        return true;
    }
}

/**
 * getResponse_keys.
 * @since PAY_CHECK_MATE_SINCE
 *
 * @param $captcha
 *
 * @return mixed
 */
function getResponse_keys( $captcha ) {
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

    return json_decode( $response, true );
}

add_action( 'init', function () {
    $enable = get_option( 'cloudflare_turnstile_enable', false);
    if ( ! $enable ) {
        return;
	}

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
