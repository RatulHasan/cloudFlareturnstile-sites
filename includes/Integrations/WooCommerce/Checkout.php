<?php

namespace RatulHasan\TurnstileForCloudflare\Integrations\WooCommerce;

use RatulHasan\TurnstileForCloudflare\Utils;

class Checkout {

	public static function init() {
		add_action( 'wp_head', [ __CLASS__, 'enqueueScripts' ] );
		add_action( 'woocommerce_review_order_after_payment', [ __CLASS__, 'addTurnstileToCheckout' ] );
		add_action( 'woocommerce_review_order_before_payment', [ __CLASS__, 'addTurnstileToCheckout' ] );
		add_action( 'woocommerce_before_checkout_billing_form', [ __CLASS__, 'addTurnstileToCheckout' ] );
		add_action( 'woocommerce_after_checkout_billing_form', [ __CLASS__, 'addTurnstileToCheckout' ] );
		add_action( 'woocommerce_review_order_before_submit', [ __CLASS__, 'addTurnstileToCheckout' ] );
		add_action( 'cfw_after_cart_summary_totals', [ __CLASS__, 'addTurnstileToCheckout' ] );
		add_action( 'woocommerce_checkout_process', [ __CLASS__, 'validateTurnstileCheckout' ] );
	}

	public static function enqueueScripts() {
		if ( get_option( 'cloudflare_turnstile_enable', false ) ) {
			wp_enqueue_script( 'turnstile-for-cloudflare', 'https://challenges.cloudflare.com/turnstile/v0/api.js', [], CFTS_VERSION, true );
		}
	}

	public static function addTurnstileToCheckout() {
		echo '<h3>' . __( 'Please complete the captcha to proceed', 'turnstile-for-checkout' ) . '</h3>';
		echo '<div class="cf-turnstile" data-sitekey="' . esc_attr( self::get_site_key() ) . '"></div>';
		wp_nonce_field( 'cf_turnstile_form_action', 'cf_turnstile_form_nonce' );
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
