<?php
/**
 * Plugin Name: Twitter Widget by Campaign Kit
 * Description: Display twitter feed by using widget, function and shortcode.
 * Plugin URI: https://bitbucket.org/campaignkitdev/campaignkit-twitter
 * Author: Campaign Kit
 * Version: 1.0.1
 * Author URI: https://campaignkit.co/
 * Text Domain: campaign-kit-twitter-widget
 * Domain Path: /languages
 *
 * @package   CampaignKit_Twitter
 * @version   1.0.0
 */

if ( ! function_exists( 'campaignkit_twitter_get_data' ) ) :
/**
 * Get plugin data
 *
 * @param  $data [plugin-slug]
 * @access public
 * @since 1.0.0
 * @return void
 */
function campaignkit_twitter_get_data( $data ) {

	if ( ! function_exists( 'get_plugins' ) ){
	    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	$plugin_folder 	= get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file 	= basename( ( __FILE__ ) );

	return $plugin_folder[$plugin_file][$data];

}
endif;

/** Plugin constant */
define( 'CAMPAIGNKIT_TWITTER_NAME', campaignkit_twitter_get_data( 'Name' ) );
define( 'CAMPAIGNKIT_TWITTER_VERSION', campaignkit_twitter_get_data( 'Version' ) );
define( 'CAMPAIGNKIT_TWITTER_PLUGIN_URL', campaignkit_twitter_get_data( 'PluginURI' ) );
define( 'CAMPAIGNKIT_TWITTER_TEXT_DOMAIN', campaignkit_twitter_get_data( 'TextDomain' ) );

define( 'CAMPAIGNKIT_TWITTER_DIR', wp_normalize_path( plugin_dir_path( __FILE__ ) ) );
define( 'CAMPAIGNKIT_TWITTER_URI', plugin_dir_url( __FILE__ ) );

/**
 * Load gettext translate for our text domain and the plugin file.
 *
 * @since 1.0.0
 * @return void
 */
function campaignkit_twitter_load_text_domain() {

	load_plugin_textdomain( 'campaign-kit-twitter-widget' );

}
add_action( 'plugins_loaded', 'campaignkit_twitter_load_text_domain' );

/**
 * Admin notice for minimum PHP version.
 *
 * Warning when the site doesn't have the minimum required PHP version.
 *
 * @since 1.0.0
 * @return void
 */
function campaignkit_twitter_fail_php_version() {
	/* translators: %1$s: Plugin name, %2$s: PHP version */
	$message = sprintf( esc_html__( '%1$s requires PHP version %2$s+, plugin is currently NOT ACTIVE.', 'campaign-kit-twitter-widget' ),
		esc_attr( CAMPAIGNKIT_TWITTER_NAME ),
		'5.4'
	);
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}

if ( ! version_compare( PHP_VERSION, '5.4', '>=' ) ) {
	add_action( 'admin_notices', 'campaignkit_twitter_fail_php_version' );
} else {
	require_once( CAMPAIGNKIT_TWITTER_DIR . 'includes/plugin.php' );
}

if ( ! function_exists( 'campaignkit_twitter_get_tweets' ) ) :
/**
 * Wrapper function of \CampaignKitTwitter\Tweets::get_tweets( $config );
 *
 * @param  array  $config [description]
 * @return [type]         [description]
 */
function campaignkit_twitter_get_tweets( $args = array() ) {

	return \CampaignKitTwitter\Tweets::get_tweets( $args );

}
endif;

if ( ! function_exists( 'campaignkit_twitter_get_tweets_html' ) ) :
/**
 * Wrapper function of \CampaignKitTwitter\Tweets::get_tweets_html( $config );
 *
 * @param  array  $config [description]
 * @return [type]         [description]
 */
function campaignkit_twitter_get_tweets_html( $args = array() ) {

	return \CampaignKitTwitter\Tweets::get_tweets_html( $args );

}
endif;
