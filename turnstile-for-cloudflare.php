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

// Autoload dependencies.
require_once __DIR__ . '/vendor/autoload.php';

// Define the plugin version.
define( 'CFTS_VERSION', '1.0.0' );
define( 'CFTS_FILE', plugin_basename( __FILE__ ) );

// Initialize the plugin.
function turnstile_for_cloudflare_init() {
    RatulHasan\TurnstileForCloudflare\Settings::init();
    RatulHasan\TurnstileForCloudflare\Admin::init();
    RatulHasan\TurnstileForCloudflare\Frontend::init();
	RatulHasan\TurnstileForCloudflare\Integrations\WooCommerce::init();
}

function cloudflare_turnstile_redirect() {
    add_option( 'cloudflare_turnstile_redirect', true );
    add_option( 'cloudflare_turnstile_version', CFTS_VERSION );
}

register_activation_hook( __FILE__, 'cloudflare_turnstile_redirect' );

// After activation redirect to settings page
add_action( 'admin_init', function () {
    if ( get_option( 'cloudflare_turnstile_redirect', false ) ) {
        delete_option( 'cloudflare_turnstile_redirect' );
        wp_safe_redirect( admin_url( 'options-general.php?page=turnstile-for-cloudflare' ) );
    }
} );

// Add language support
function cloudflare_turnstile_load_textdomain() {
    load_plugin_textdomain( 'turnstile-for-cloudflare', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'init', 'cloudflare_turnstile_load_textdomain' );

turnstile_for_cloudflare_init();


if ( ! function_exists( 'write_log' ) ) {
	function write_log ( $log )  {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}
}

