<?php
/**
 * Main class for the plugin
 *
 * @package CampaignKit_Twitter
 */

namespace CampaignKitTwitter;

class plugin {

	private static $_instance;

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'campaign-kit-twitter-widget' ), '1.0.0' );

	}

	/**
	 * Disable unserializing of the class
	 *
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'campaign-kit-twitter-widget' ), '1.0.0' );

	}

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
	 * Include the library
	 *
	 * @return void
	 */
	private function includes() {

		require CAMPAIGNKIT_TWITTER_DIR . 'includes/options.php';

		if ( is_admin() ) {
			require CAMPAIGNKIT_TWITTER_DIR . 'includes/admin.php';
		}

		if ( ! class_exists( 'TwitterAPIExchange' ) ) {
			require CAMPAIGNKIT_TWITTER_DIR . 'includes/lib/TwitterAPIExchange.php';
		}
		require CAMPAIGNKIT_TWITTER_DIR . 'includes/lib/Tweets.php';

		/** Twitter Widget */
		require CAMPAIGNKIT_TWITTER_DIR . 'includes/widget.php';

	}

	/**
	 * [assets description]
	 * @return [type] [description]
	 */
	public function assets() {

		if ( apply_filters( 'campaignkit_twitter_load_style', '__return_true' ) == false ) {
			return;
		}

		$options 	= Options::get_option();
		$suffix 	= defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style(
			'campaignkit-twitter-style',
			CAMPAIGNKIT_TWITTER_URI . "assets/css/frontend{$suffix}.css",
			[],
			CAMPAIGNKIT_TWITTER_VERSION,
			'all'
		);

	}

	public function twitter_widgets_js() {

		if ( apply_filters( 'campaignkit_twitter_load_script', '__return_true' ) == false ) {
			return;
		}

		$script = '
		<script>
			window.twttr = (function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0],
				t = window.twttr || {};
				if (d.getElementById(id)) return t;
				js = d.createElement(s);
				js.id = id;
				js.src = "https://platform.twitter.com/widgets.js";
				fjs.parentNode.insertBefore(js, fjs);

				t._e = [];
				t.ready = function(f) {
					t._e.push(f);
				};

				return t;
			}(document, "script", "twitter-wjs"));
		</script>
		';

		$script = str_replace( array( "\n", "\t", "\r" ), '', $script );

		echo $script;

	}

	/**
	 * [register_widget description]
	 * @return [type] [description]
	 */
	public function register_widget() {
		register_widget( '\CampaignKitTwitter\Widget' );
	}


	/**
	 * [shorcode description]
	 * @param  [type] $atts [description]
	 * @return [type]       [description]
	 */
	public function shortcode( $atts ) {

		$defaults = Options::config();

		$atts = shortcode_atts( $defaults, $atts, 'ck_tweets' );

		return Tweets::get_tweets_html( $atts );

	}

	/**
	 * Hooks
	 *
	 * @return void
	 */
	private function hooks() {

		add_action( 'wp_enqueue_scripts', [ $this, 'assets'], 10 );
		add_action( 'wp_head', [ $this, 'twitter_widgets_js' ], 5 );

		add_action( 'widgets_init', [ $this, 'register_widget' ] );

		add_shortcode( 'ck_tweets', [ $this, 'shortcode' ] );

	}

	/**
	 * Constructor.
	 */
	private function __construct() {

		$this->includes();
		$this->hooks();

	}

}

Plugin::instance();
