<?php

namespace RatulHasan\TurnstileForCloudflare\Integrations\WooCommerce;

use RatulHasan\TurnstileForCloudflare\Utils;

class Checkout {

	public static function init() {
		add_action( 'woocommerce_review_order_before_submit', [ __CLASS__, 'addTurnstileToCheckout' ] );
		add_action( 'woocommerce_checkout_process', [ __CLASS__, 'validateTurnstileCheckout' ] );
	}

	public static function addTurnstileToCheckout() {
		echo '<pre>';
		print_r( 'ok' );
		exit();
		echo '<h3>' . __( 'Please complete the captcha to proceed', 'turnstile-for-checkout' ) . '</h3>';
		echo '<div class="cf-turnstile" data-sitekey="' . esc_attr( self::get_site_key() ) . '"></div>';
		wp_nonce_field( 'cf_turnstile_form_action', 'cf_turnstile_form_nonce' );
		wp_enqueue_script( 'turnstile-for-checkout', 'https://challenges.cloudflare.com/turnstile/v0/api.js', [], '1.0.0', true );

		// Debugging statement
		error_log( 'Turnstile added to checkout.' );
		echo '<script>console.log("Turnstile added to checkout.");</script>';
	}

	public static function validateTurnstileCheckout() {
		if ( empty( $_POST['cf-turnstile-response'] ) ) {
			wc_add_notice( __( 'Captcha validation failed. Please try again.', 'turnstile-for-checkout' ), 'error' );

			return;
		}
		Utils::validateNonce( 'cf_turnstile_form_nonce', 'cf_turnstile_form_action' );

		$captcha = sanitize_text_field( $_POST['cf-turnstile-response'] );
		if ( Utils::isValidCaptcha( $captcha ) ) {
			wc_add_notice( __( 'Captcha validation failed. Please try again.', 'turnstile-for-checkout' ), 'error' );
		}
	}

	private static function get_site_key() {
		return Utils::getKeys( 'cloudflare_site_key' );
	}
}
