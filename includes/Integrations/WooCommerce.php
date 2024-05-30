<?php

namespace RatulHasan\TurnstileForCloudflare\Integrations;

use RatulHasan\TurnstileForCloudflare\Integrations\WooCommerce\Checkout;

class WooCommerce {

	public static function init() {
		add_action('woocommerce_loaded', [__CLASS__, 'woocommerceLoaded']);
	}

	public static function woocommerceLoaded() {
		Checkout::init();
	}

}
