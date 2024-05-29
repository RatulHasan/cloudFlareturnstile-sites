<?php
namespace RatulHasan\TurnstileForCloudflare;

class Settings {
    public static function init() {
        add_action( 'admin_init', [ __CLASS__, 'registerSettings' ] );
        add_action( 'admin_menu', [ __CLASS__, 'addMenu' ] );
        add_filter( 'plugin_action_links_' . CFTS_FILE, [ __CLASS__, 'settingsLink' ] );
    }

    public static function registerSettings() {
        register_setting( 'cloudflare_turnstile', 'cloudflare_turnstile_enable' );
        register_setting( 'cloudflare_turnstile', 'cloudflare_site_key' );
        register_setting( 'cloudflare_turnstile', 'cloudflare_secret_key' );
        register_setting( 'cloudflare_turnstile', 'cloudflare_turnstile_login_enable' );
        register_setting( 'cloudflare_turnstile', 'cloudflare_turnstile_comment_enable' );
        register_setting( 'cloudflare_turnstile', 'cloudflare_turnstile_register_enable' );
        register_setting( 'cloudflare_turnstile', 'cloudflare_turnstile_lostpassword_enable' );
    }

    public static function addMenu() {
        add_options_page(
            'Turnstile for Cloudflare',
            'Turnstile for Cloudflare',
            'manage_options',
            'turnstile-for-cloudflare',
            [ __CLASS__, 'settingsPage' ]
        );
    }

    public static function settingsLink( $links ) {
        $settings_link = '<a href="options-general.php?page=turnstile-for-cloudflare">' . __( 'Settings', 'turnstile-for-cloudflare' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    public static function settingsPage() {
        ?>
	    <div class='wrap'>
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
			        <tr>
						<th scope="row"><label for="cloudflare_turnstile_register_enable"><?php
	                            esc_html_e( 'Enable in Register Form', 'turnstile-for-cloudflare' ); ?></label></th>
						<td>
							<input
								type="checkbox"
								id="cloudflare_turnstile_register_enable"
								name="cloudflare_turnstile_register_enable"
								value="1" <?php
	                        checked( 1, get_option( 'cloudflare_turnstile_register_enable' ), true ); ?>>
						</td>
			        </tr>
			        <tr>
						<th scope="row">
							<label for="cloudflare_turnstile_lostpassword_enable">
								<?php
	                            esc_html_e( 'Enable in Lost Password Form', 'turnstile-for-cloudflare' );
	                            ?>
							</label>
						</th>
						<td>
							<input
								type="checkbox"
								id="cloudflare_turnstile_lostpassword_enable"
								name="cloudflare_turnstile_lostpassword_enable"
								value="1" <?php
	                        checked( 1, get_option( 'cloudflare_turnstile_lostpassword_enable' ), true ); ?>>
						</td>
			        </tr>

	            </table>
	            <?php
	            submit_button(); ?>
	        </form>
	    </div>
        <?php
    }
}
