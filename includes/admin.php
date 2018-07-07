<?php
/**
 * Admin setting
 *
 * @package CampaignKit_Twitter
 */

namespace CampaignKitTwitter;

class Admin {

	private static $_instance;

	/**
	 * Instance
	 *
	 * @return void
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;

	}

	/**
	 * [notification description]
	 * @return [type] [description]
	 */
	public function notification() {

		if ( true === (bool) get_option( 'campaignkit_twitter_notice_dismissed' ) ) {
			return;
		}

		add_action( 'admin_notices', [ $this, 'message' ] );

	}

	/**
	 * [message description]
	 * @return [type] [description]
	 */
	public function message() {

		$message = sprintf( __( 'Please insert your Twitter API credential to provide authorized access to its API. <a href="%s">Setup Twitter API &rarr;</a>.', 'campaign-kit-twitter-widget' ),
			self_admin_url( 'options-general.php?page=campaignkit-twitter-admin' )
		);

		printf( '<div class="notice notice-error is-dismissible campaignkit-twitter-notice">%s</div>', wp_kses_post( wpautop( $message ) ) );

	}

	/**
	 * [scripts description]
	 * @return [type] [description]
	 */
	public function scripts() {

		$suffix 	= defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'campaignkit-twitter-admin',
			CAMPAIGNKIT_TWITTER_URI . '/assets/js/admin' . $suffix . '.js',
			[ 'jquery' ],
			CAMPAIGNKIT_TWITTER_VERSION,
			true
		);

		$localize = array(
			'nonce' => wp_create_nonce( 'campaignkit_twitter_notice_dismiss' )
		);

		wp_localize_script( 'campaignkit-twitter-admin', 'CampaignKitTwitter', $localize );

	}

	/**
	 * [dismiss_notice description]
	 * @return [type] [description]
	 */
	public function dismiss_notice() {

		$nonce = ! empty( $_POST['nonce'] ) ? $_POST['nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'campaignkit_twitter_notice_dismiss' ) || ! current_user_can( 'manage_options' ) ) {
			die();
		}

		update_option( 'campaignkit_twitter_notice_dismissed', true );

	}

	/**
	 * Check credentials
	 *
	 * @return bool
	 */
	public function requirement() {

		$keys = Options::keys();

		$consumer_key 				= $keys['consumer_key'];
		$consumer_secret 			= $keys['consumer_secret'];
		$oauth_access_token 		= $keys['oauth_access_token'];
		$oauth_access_token_secret 	= $keys['oauth_access_token_secret'];

		if ( ! empty( $consumer_key ) && ! empty( $consumer_secret ) && ! empty( $oauth_access_token ) && ! empty( $oauth_access_token_secret ) ) {
			return (bool) true;
		} else {
			return (bool) false;
		}

	}

	/**
	 * Verify credential data
	 *
	 * @return [type] [description]
	 */
	public function verify_credentials() {

		$settings 		= Options::keys();
		$prefix 		= str_replace( '-', '_', sanitize_title_with_dashes( CAMPAIGNKIT_TWITTER_TEXT_DOMAIN ) );
		$transient_name = $prefix . '_verify_credentials';

		$credential 	= get_transient( $transient_name );

		if ( false === (bool) $this->requirement() ) {
			return;
		}

		if ( $credential !== false ) {
			return $credential;
		}

		$url 			= 'https://api.twitter.com/1.1/account/verify_credentials.json';
		$getfield 		= '?skip_status=1';
		$requestMethod 	= 'GET';

		$twitter = new \TwitterAPIExchange( $settings );

		$credential = $twitter->setGetfield( $getfield )
		    ->buildOauth( $url, $requestMethod )
		    ->performRequest();

		$credential = json_decode( $credential, true );

		set_transient( $transient_name, $credential, apply_filters( 'campaignkit_twitter_credential_expiration', 1 ) * DAY_IN_SECONDS );

		return $credential;

	}

	/**
	 * Plugin row meta
	 *
	 * @param  [type] $links [description]
	 * @param  [type] $file  [description]
	 * @return [type]        [description]
	 */
	public function plugin_row_meta( $links, $file ) {

		if ( strpos( $file, 'campaignkit-twitter.php' ) !== false ) {

			$new_links = [
				'setting' => sprintf( '<a href="%1$s" aria-label="%2$s">%3$s</a>',
					self_admin_url( 'options-general.php?page=campaignkit-twitter-admin' ),
					esc_html__( 'Configure Twitter API setting', 'campaign-kit-twitter-widget' ),
					esc_html__( 'Setting', 'campaign-kit-twitter-widget' ) )
			];

			$links = array_merge( $links, $new_links );
		}

		return $links;

	}

	/**
	 * [admin_menu description]
	 * @return [type] [description]
	 */
	public function admin_menu() {

    	$title = esc_html__( 'CampaignKit - Twitter', 'campaign-kit-twitter-widget' );
    	add_options_page(
    		$title,
    		$title,
    		'manage_options',
    		'campaignkit-twitter-admin',
    		[ $this, 'render_page']
    	);

	}

	/**
	 * [settings_init description]
	 * @return [type] [description]
	 */
	public function settings_init() {

		register_setting( 'campaignkit_twitter_settings', 'campaignkit_twitter_settings', [ $this, 'validate_api_key' ] );

		add_settings_section( 'campaignkit_twitter_section',
			__( 'Twitter API Settings', 'campaign-kit-twitter-widget' ),
			[ $this, 'section_callback' ],
			'campaignkit_twitter_settings'
		);

		add_settings_field( 'consumer_key',
			__( 'Consumer Key', 'campaign-kit-twitter-widget' ),
			[ $this, 'consumer_key_render' ],
			'campaignkit_twitter_settings',
			'campaignkit_twitter_section'
		);

		add_settings_field( 'consumer_secret',
			__( 'Consumer Secret', 'campaign-kit-twitter-widget' ),
			[ $this, 'consumer_secret_render' ],
			'campaignkit_twitter_settings',
			'campaignkit_twitter_section'
		);

		add_settings_field( 'oauth_access_token',
			__( 'Oauth Access Token', 'campaign-kit-twitter-widget' ),
			[ $this, 'oauth_access_token_render' ],
			'campaignkit_twitter_settings',
			'campaignkit_twitter_section'
		);

		add_settings_field( 'oauth_access_token_secret',
			__( 'Oauth Access Secret', 'campaign-kit-twitter-widget' ),
			[ $this, 'oauth_access_token_secret_render' ],
			'campaignkit_twitter_settings',
			'campaignkit_twitter_section'
		);

	}

	/**
	 * [consumer_key_render description]
	 * @return [type] [description]
	 */
	public function consumer_key_render() {

		$options = Options::get_option();
		?>
		<input type='text' name='campaignkit_twitter_settings[consumer_key]' size='60' value='<?php echo $options['consumer_key']; ?>'>
		<?php

	}

	/**
	 * [consumer_secret_render description]
	 * @return [type] [description]
	 */
	public function consumer_secret_render() {

		$options = Options::get_option();
		?>
		<input type='text' name='campaignkit_twitter_settings[consumer_secret]' size='60' value='<?php echo $options['consumer_secret']; ?>'>
		<?php

	}

	/**
	 * [oauth_access_token_render description]
	 * @return [type] [description]
	 */
	public function oauth_access_token_render() {

		$options = Options::get_option();
		?>
		<input type='text' name='campaignkit_twitter_settings[oauth_access_token]' size='60' value='<?php echo $options['oauth_access_token']; ?>'>
		<?php

	}

	/**
	 * [oauth_access_token_secret_render description]
	 * @return [type] [description]
	 */
	public function oauth_access_token_secret_render() {

		$options = Options::get_option();
		?>
		<input type='text' name='campaignkit_twitter_settings[oauth_access_token_secret]' size='60' value='<?php echo $options['oauth_access_token_secret']; ?>'>
		<?php

	}

	/**
	 * [section_callback description]
	 * @return [type] [description]
	 */
	public function section_callback() {

		echo sprintf( wp_kses_post( __( 'Get API key from your <a href="%s" target="_blank">Twitter dashboard</a>. Click toggle the help tab to get more information.', 'campaign-kit-twitter-widget' ) ), esc_url( 'https://dev.twitter.com/apps' ) );

	}

	/**
	 * [validate_api_key description]
	 * @param  [type] $input [description]
	 * @return [type]        [description]
	 */
	public function validate_api_key( $input ) {

		$credential 	= $this->verify_credentials();
		$prefix 		= str_replace( '-', '_', sanitize_title_with_dashes( CAMPAIGNKIT_TWITTER_TEXT_DOMAIN ) );
		$transient_name = $prefix . '_verify_credentials';
		$options 		= Options::get_option();
		$error 			= 0;

		delete_transient( $transient_name );

		$options['consumer_key'] = trim( $input['consumer_key'] );
		if( ! preg_match( '/^[a-z0-9]+$/i', $options['consumer_key'] ) ) {
			$options['consumer_key'] = '';
			$error = 1;
			add_settings_error( 'campaignkit_twitter_settings', esc_attr( 'campaignkit-twitter-error-consumer_key' ), esc_html__( 'Error: Twitter API Consumer Key not correctly formatted.', 'campaign-kit-twitter-widget' ) );
		}

		$options['consumer_secret'] = trim( $input['consumer_secret'] );
		if( ! preg_match( '/^[a-z0-9]+$/i', $options['consumer_secret'] ) ) {
			$options['consumer_secret'] = '';
			$error = 1;
			add_settings_error( 'campaignkit_twitter_settings', esc_attr( 'campaignkit-twitter-error-consumer_secret' ), esc_html__( 'Error: Twitter API Consumer Secret not correctly formatted.', 'campaign-kit-twitter-widget' ) );
		}

		$options['oauth_access_token'] = trim( $input['oauth_access_token'] );
		if( ! preg_match( '/^[a-z0-9]+\-[a-z0-9]+$/i', $options['oauth_access_token'] ) ) {
			$options['oauth_access_token'] = '';
			$error = 1;
			add_settings_error( 'campaignkit_twitter_settings', esc_attr( 'campaignkit-twitter-error-oauth_access_token' ), esc_html__( 'Error: Twitter API oAuth Access Token not correctly formatted.', 'campaign-kit-twitter-widget' ) );
		}

		$options['oauth_access_token_secret'] = trim( $input['oauth_access_token_secret'] );
		if( ! preg_match( '/^[a-z0-9]+$/i', $options['oauth_access_token_secret'] ) ) {
			$options['oauth_access_token_secret'] = '';
			$error = 1;
			add_settings_error( 'campaignkit_twitter_settings', esc_attr( 'campaignkit-twitter-error-oauth_access_token_secret' ), esc_html__( 'Error: Twitter API oAuth Access Token Secret not correctly formatted.', 'campaign-kit-twitter-widget' ) );
		}

		return $options;

	}

	/**
	 * [render_page description]
	 * @return [type] [description]
	 */
	public function render_page() {

		$credential 	= $this->verify_credentials();

		?>
		<div class="wrap">
			<h2><?php echo esc_html__( 'Campaign Kit - Twitter Settings', 'campaign-kit-twitter-widget' )?></h2>

			<?php

			if ( isset( $credential['errors'] ) ) {

				$message = sprintf( esc_html__( 'Error message received from Twitter: %1$s Error code: %2$s', 'campaign-kit-twitter-widget' ),
					esc_attr( $credential['errors'][0]['message'] ),
					absint( $credential['errors'][0]['code'] )
				);

				echo '<div class="error settings-error notice is-dismissible"><p>'. wp_kses_post( $message ) .'</p></div>';

			} elseif( isset( $credential['id'] ) ) {

				/** Translator: %s is connected Twitter user */
				$message = sprintf( __( 'Conected as %s', 'campaign-kit-twitter-widget' ),
					'<a href="'. esc_url( esc_url( $credential['url'] ) ) .'" target="_blank">@'. esc_attr( $credential['screen_name'] ) .'</a>'
				);

				echo '<div class="notice updated"><p>'. wp_kses_post( $message ) .'</p></div>';
			}


			?>

			<form action='options.php' method='post'>
				<?php

				settings_fields( 'campaignkit_twitter_settings' );

				do_settings_sections( 'campaignkit_twitter_settings' );

				submit_button( esc_html__( 'Save Changes', 'campaign-kit-twitter-widget' ) );

				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Help screen
	 *
	 * @return [type] [description]
	 */
	public function help_screen() {

		$current_screen = get_current_screen();

		if ( ! isset( $current_screen->base ) ) {
			return;
		}

		if ( $current_screen->base === 'settings_page_campaignkit-twitter-admin' ) {

			$content_api = '';

			$content_api .= '<ol>';

				$content_api .= sprintf( '<li>%s</li>',
					/* translators: %s : My Application */
					sprintf( __( 'Go to the %s page on the Twitter website to set up your website as a new Twitter application. You may need to log-in using your Twitter user name and password.', 'campaign-kit-twitter-widget' ),
						'<a href="'. esc_url( 'https://dev.twitter.com/apps' ) .'">'. esc_html__( 'My Application', 'campaign-kit-twitter-widget' ) .'</a>'
					)
				);

				$content_api .= sprintf( '<li>%s</li>',
					esc_html__( 'If you don\'t already have a suitable application that you can use for your website, set one up on the Create an Application page.', 'campaign-kit-twitter-widget' )
				);

				$content_api .= sprintf( '<li>%s</li>',
					esc_html__( 'After clicking Create your Twitter application, on the following page, click on Create my access token.', 'campaign-kit-twitter-widget' )
				);

				$content_api .= sprintf( '<li>%s</li>',
					esc_html__( 'Copy the Consumer key, Consumer secret, Access token and Access token secret from your Twitter application page into the settings below.', 'campaign-kit-twitter-widget' )
				);

				$content_api .= sprintf( '<li>%s</li>',
					esc_html__( 'Click on Save Changes.', 'campaign-kit-twitter-widget' )
				);

			$content_api .= '</ol>';

	        $current_screen->add_help_tab( array(
	                'id'        => 'campaingkit_twitter_admin_help',
	                'title'     => esc_html__( 'Configure Twitter API', 'campaign-kit-twitter-widget' ),
	                'content'   => wp_kses_post( $content_api ),
	            )
	        );

	        $usage_help = '';

	        $usage_help .= sprintf( wpautop( wp_kses_post( __( 'Please go to %s for more detailed documentation on how to use these features below:', 'campaign-kit-twitter-widget' ) ) ),
	        	'<a href="'. esc_url( 'https://wordpress.org/plugins/campaignkit-twitter/installation' ) .'" traget="_blank">'. esc_html__( 'installation page', 'campaign-kit-twitter-widget' ) .'</a>'
	        );

	        $usage_help .= '<ol>';

	        	$usage_help .= sprintf( '<li>%s</li>',
					wp_kses_post( __( 'Widget', 'campaign-kit-twitter-widget' ) )
	        	);

	        	$usage_help .= sprintf( '<li>%s</li>',
					wp_kses_post( __( 'Shortcode', 'campaign-kit-twitter-widget' ) )
	        	);

	        	$usage_help .= sprintf( '<li>%s</li>',
					wp_kses_post( __( 'Functions', 'campaign-kit-twitter-widget' ) )
	        	);

	        	$usage_help .= sprintf( '<li>%s</li>',
					wp_kses_post( __( 'Advanced usage', 'campaign-kit-twitter-widget' ) )
	        	);

	        $usage_help .= '</ol>';

	        $current_screen->add_help_tab( array(
	                'id'        => 'campaingkit_twitter_admin_help_usage',
	                'title'     => esc_html__( 'How to', 'campaign-kit-twitter-widget' ),
	                'content'   => wp_kses_post( $usage_help ),
	            )
	        );

	        $sidebar = '';

	        $sidebar .= '<ul>';
	        	$sidebar .= sprintf( '<li>%s</li>',
	        		sprintf( '<a href="%1$s">%2$s</a>', esc_url( 'https://wordpress.org/plugins/campaignkit-twitter/installation' ), esc_html__( 'Documentation', 'campaign-kit-twitter-widget' ) )
	        	);
	        	$sidebar .= sprintf( '<li>%s</li>',
	        		/* translators: %s : Plugin author */
	        		sprintf( esc_html__( 'Design and developed by %s.', 'campaign-kit-twitter-widget' ), '<a href="'. esc_url( 'http://campaignkit.io' ) .'">Campaign Kit</a>' )
	        	);
	        $sidebar .= '</ul>';

	        $current_screen->set_help_sidebar( wp_kses_post( $sidebar ) );

		}

	}

	/**
	 * [admin_footer_text description]
	 *
	 * @param  [type] $footer_text [description]
	 * @return [type]              [description]
	 */
	public function admin_footer_text( $footer_text ) {

		$current_screen = get_current_screen();

		if ( $current_screen->base == 'settings_page_campaignkit-twitter-admin' ) {
			$footer_text = sprintf(
				/* translators: %1$s: Plugin name, %2$s: Link to plugin review */
				__( 'Enjoyed <strong>%1$s</strong>? Please leave us a %2$s rating. We really appreciate your support!', 'campaign-kit-twitter-widget' ),
				esc_attr( CAMPAIGNKIT_TWITTER_NAME ),
				'<a href="https://wordpress.org/support/plugin/campaignkit-twitter/reviews/?filter=5#new-post" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}

		return $footer_text;

	}

	/**
	 * Hooks
	 *
	 * @return void
	 */
	private function hooks() {

		if ( false === (bool) $this->requirement() ) {
			add_action( 'plugins_loaded', [ $this, 'notification' ] );
		}

		add_action( 'wp_ajax_campaignkit_twitter_dismiss_notice', [ $this, 'dismiss_notice' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );

		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );

		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_init', [ $this, 'settings_init' ] );
		add_action( 'admin_head', [ $this, 'help_screen' ] );
		add_filter( 'admin_footer_text', [ $this, 'admin_footer_text' ] );

	}

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->hooks();

	}

}

Admin::instance();
