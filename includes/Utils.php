<?php
namespace RatulHasan\TurnstileForCloudflare;

class Utils {
    public static function getKeys() {
        return [
            get_option( 'cloudflare_site_key', '' ),
            get_option( 'cloudflare_secret_key', '' )
        ];
    }

    public static function isValidCaptcha( $captcha ) {
        if ( ! $captcha ) {
            return false;
        }
        $responseKeys = self::getResponseKeys( $captcha );
        return intval( $responseKeys['success'] ) === 1;
    }

    public static function getResponseKeys( $captcha ) {
        $secretKey = self::getKeys()[1];
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
            $error_message = $response->get_error_message();
            echo 'Something went wrong: ' . esc_html( $error_message );
        }

        $result = wp_remote_retrieve_body( $response );
        return json_decode( $result, true );
    }

    public static function validateNonce($postName, $nonceName) {
        if ( ! isset( $_POST[$nonceName] ) || ! wp_verify_nonce( $_POST[$nonceName], $postName ) ) {
            wp_die( __( 'Are you cheating?', 'turnstile-for-cloudflare' ) );
        }
    }
}
