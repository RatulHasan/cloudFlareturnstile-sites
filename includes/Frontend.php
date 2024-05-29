<?php
namespace RatulHasan\TurnstileForCloudflare;

class Frontend {
    public static function init() {
        add_action( 'wp_head', [ __CLASS__, 'enqueueScripts' ] );
        add_action( 'login_enqueue_scripts', [ __CLASS__, 'enqueueLoginScripts' ] );
        add_action( 'login_form', [ __CLASS__, 'addLoginCaptcha' ] );
        add_action( 'wp_authenticate_user', [ __CLASS__, 'validateLoginCaptcha' ], 10, 2 );
        add_filter( 'comment_form_defaults', [ __CLASS__, 'addCommentCaptcha' ] );
        add_action( 'pre_comment_on_post', [ __CLASS__, 'validateCommentCaptcha' ] );
        add_action( 'register_form', [ __CLASS__, 'addRegisterCaptcha' ] );
        add_filter( 'registration_errors', [ __CLASS__, 'validateRegisterCaptcha' ], 10, 3 );
        add_action( 'lostpassword_form', [ __CLASS__, 'addLostPasswordCaptcha' ] );
        add_action( 'lostpassword_post', [ __CLASS__, 'validateLostPasswordCaptcha' ] );
    }

    public static function enqueueScripts() {
        if ( get_option( 'cloudflare_turnstile_enable', false ) ) {
            wp_enqueue_script( 'turnstile-for-cloudflare', 'https://challenges.cloudflare.com/turnstile/v0/api.js', [], CFTS_VERSION, true );
        }
    }

    public static function enqueueLoginScripts() {
        if ( get_option( 'cloudflare_turnstile_enable', false ) ) {
            wp_enqueue_script( 'turnstile-for-cloudflare', 'https://challenges.cloudflare.com/turnstile/v0/api.js', [], CFTS_VERSION, true );
        }
    }

    public static function addLoginCaptcha() {
        if ( get_option( 'cloudflare_turnstile_enable', false ) && get_option( 'cloudflare_turnstile_login_enable', false ) ) {
            echo '<div class="cf-turnstile" data-sitekey="' . esc_attr( Utils::getKeys()[0] ) . '"></div>';
            wp_nonce_field( 'cf_turnstile_form_action', 'cf_turnstile_form_nonce' );
        }
    }

    public static function validateLoginCaptcha( $user, $password ) {
        if ( get_option( 'cloudflare_turnstile_enable', false ) && get_option( 'cloudflare_turnstile_login_enable', false ) ) {
            Utils::validateNonce( 'cf_turnstile_form_nonces', 'cf_turnstile_form_action' );
            $captcha = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( $_POST['cf-turnstile-response'] ) : '';
            if ( ! Utils::isValidCaptcha( $captcha ) ) {
                return new \WP_Error( 'captcha_error', __( 'Captcha Invalid', 'turnstile-for-cloudflare' ) );
            }
        }
        return $user;
    }

    public static function addCommentCaptcha( $defaults ) {
        if ( get_option( 'cloudflare_turnstile_enable', false ) && get_option( 'cloudflare_turnstile_comment_enable', false ) ) {
            $defaults['comment_field'] .= '<div class="cf-turnstile" data-sitekey="' . esc_attr( Utils::getKeys()[0] ) . '"></div>';
            wp_nonce_field( 'cf_turnstile_form_action', 'cf_turnstile_form_nonce' );
        }
        return $defaults;
    }

    public static function validateCommentCaptcha() {
        if ( get_option( 'cloudflare_turnstile_enable', false ) && get_option( 'cloudflare_turnstile_comment_enable', false ) ) {
            Utils::validateNonce( 'cf_turnstile_form_nonce', 'cf_turnstile_form_action' );
            $captcha = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( $_POST['cf-turnstile-response'] ) : '';
            if ( ! Utils::isValidCaptcha( $captcha ) ) {
                wp_die( esc_html__( 'Captcha Invalid', 'turnstile-for-cloudflare' ) );
            }
        }
    }

    public static function addLostPasswordCaptcha() {
        if ( get_option( 'cloudflare_turnstile_enable', false ) && get_option( 'cloudflare_turnstile_lostpassword_enable', false ) ) {
            echo '<div class="cf-turnstile" data-sitekey="' . esc_attr( Utils::getKeys()[0] ) . '"></div>';
            wp_nonce_field( 'cf_turnstile_form_action', 'cf_turnstile_form_nonce' );
        }
    }

    public static function validateLostPasswordCaptcha( $errors ) {
        if ( get_option( 'cloudflare_turnstile_enable', false ) && get_option( 'cloudflare_turnstile_lostpassword_enable', false ) ) {
            Utils::validateNonce( 'cf_turnstile_form_nonce', 'cf_turnstile_form_action' );
            $captcha = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( $_POST['cf-turnstile-response'] ) : '';
            if ( ! Utils::isValidCaptcha( $captcha ) ) {
                $errors->add( 'captcha_error', __( 'Captcha Invalid', 'turnstile-for-cloudflare' ) );
            }
        }
    }

    public static function addRegisterCaptcha() {
        if ( get_option( 'cloudflare_turnstile_enable', false ) && get_option( 'cloudflare_turnstile_register_enable', false ) ) {
            echo '<div class="cf-turnstile" data-sitekey="' . esc_attr( Utils::getKeys()[0] ) . '"></div>';
            wp_nonce_field( 'cf_turnstile_form_action', 'cf_turnstile_form_nonce' );
        }
    }

    public static function validateRegisterCaptcha( $errors, $sanitized_user_login, $user_email ) {
        if ( get_option( 'cloudflare_turnstile_enable', false ) && get_option( 'cloudflare_turnstile_register_enable', false ) ) {
            Utils::validateNonce( 'cf_turnstile_form_nonce', 'cf_turnstile_form_action' );
            $captcha = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( $_POST['cf-turnstile-response'] ) : '';
            if ( ! Utils::isValidCaptcha( $captcha ) ) {
                $errors->add( 'captcha_error', __( 'Captcha Invalid', 'turnstile-for-cloudflare' ) );
            }
        }
        return $errors;
    }
}
