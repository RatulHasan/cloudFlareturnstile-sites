<?php
namespace RatulHasan\TurnstileForCloudflare;

class Admin {
    public static function init() {
        add_action( 'admin_init', [ __CLASS__, 'redirectAfterActivation' ] );
        add_action( 'init', [ __CLASS__, 'loadTextdomain' ] );
    }

    public static function redirectAfterActivation() {
        if ( get_option( 'cloudflare_turnstile_redirect', false ) ) {
            delete_option( 'cloudflare_turnstile_redirect' );
            wp_redirect( admin_url( 'options-general.php?page=turnstile-for-cloudflare' ) );
        }
    }

    public static function loadTextdomain() {
        load_plugin_textdomain( 'turnstile-for-cloudflare', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }
}
