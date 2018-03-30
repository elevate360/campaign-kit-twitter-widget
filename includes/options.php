<?php
/**
 * Options
 *
 * @package CampaignKit_Twitter
 */
namespace CampaignKitTwitter;

class Options {

	public static function get_option() {

	    $option = wp_parse_args(
	        get_option( 'campaignkit_twitter_settings', array() ),
	        self::default()
	    );

	    return $option;

	}

	/**
	 * Default settings value
	 *
	 * @return array
	 */
	public static function default() {

		$default = [
	      'consumer_key' 				=> '',
	      'consumer_secret' 			=> '',
	      'oauth_access_token' 			=> '',
	      'oauth_access_token_secret' 	=> '',
		];

		return apply_filters( 'campaignkit_twitter_setting_default', $default );

	}

	/**
	 * API keys array
	 *
	 * @return array
	 */
	public static function keys() {

		$option = self::get_option();

		$keys = [
	      'consumer_key' 				=> $option['consumer_key'],
	      'consumer_secret' 			=> $option['consumer_secret'],
	      'oauth_access_token' 			=> $option['oauth_access_token'],
	      'oauth_access_token_secret' 	=> $option['oauth_access_token_secret'],
		];

		return $keys;

	}

	/**
	 * Default configuration value
	 *
	 * @see  Tweets::format_tweet_text() for template config
	 * @return array
	 */
	public static function config() {

		$default = [
			'title'				=> '',
			'screen_name' 		=> 'campaignkitau',
			'count'				=> 5,
			'exclude_replies'	=> false,
			'include_rts'		=> true,
			'tweet_mode'		=> 'extended',
			'cache_enabled'		=> true,
			'cache_expiration'	=> 5,
			'max_height'		=> 0,
			'show_cover'		=> true,
			'show_tweets'		=> true,
			'show_profile'		=> true,
			'show_media'		=> true,
			'show_meta'			=> true,
			'show_follow'		=> true,
			'follow_text'		=> esc_html__( 'Follow Me', 'campaign-kit-twitter-widget' )
		];

		return apply_filters( 'campaignkit_twitter_config_default', $default );

	}

}
