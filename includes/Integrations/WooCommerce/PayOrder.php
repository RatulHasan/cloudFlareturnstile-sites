<?php

namespace RatulHasan\TurnstileForCloudflare\Integrations\WooCommerce;

use RatulHasan\TurnstileForCloudflare\Utils;

class PayOrder {
	public static function init() {
		// Ensure WooCommerce is loaded before initializing
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueueScripts' ] );
		add_action( 'woocommerce_before_checkout_form', [ __CLASS__, 'addTurnstileToPayForOrder' ] );
		add_action( 'woocommerce_pay_order_after_submit', [ __CLASS__, 'addTurnstileToPayForOrder' ] );
		add_action( 'woocommerce_before_pay_action', [ __CLASS__, 'validateTurnstilePayForOrder' ] );
		add_filter( 'woocommerce_order_button_html', [ __CLASS__, 'addTurnstileToPayForOrder' ] );
	}

	public static function enqueueScripts() {
		// Enqueue Turnstile script
		wp_enqueue_script( 'turnstile-for-cloudflare');
	}

	public static function addTurnstileToPayForOrder($a) {
		echo '<h3>' . __( 'Please complete the captcha to proceed', 'turnstile-for-pay' ) . '</h3>';
		echo '<div class="cf-turnstile" data-sitekey="' . esc_attr( self::get_site_key() ) . '"></div>';
		wp_nonce_field( 'cf_turnstile_form_action', 'cf_turnstile_form_nonce' );

		// Debugging statement
		error_log( 'Turnstile added to Pay for Order.' );
		echo '<script>console.log("Turnstile added to Pay for Order.");</script>';
	}

	public static function validateTurnstilePayForOrder() {
		if ( empty( $_POST['cf-turnstile-response'] ) ) {
			wc_add_notice( __( 'Captcha validation failed. Please try again.', 'turnstile-for-pay' ), 'error' );

			return;
		}
		Utils::validateNonce( 'cf_turnstile_form_nonce', 'cf_turnstile_form_action' );

		$captcha = sanitize_text_field( $_POST['cf-turnstile-response'] );
		if ( ! Utils::isValidCaptcha( $captcha ) ) {
			wc_add_notice( __( 'Captcha validation failed. Please try again.', 'turnstile-for-pay' ), 'error' );
		}
	}

	private static function get_site_key() {
		return Utils::getKeys( 'cloudflare_site_key' );
	}
}
