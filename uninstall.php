<?php
/**
 * Uninstall
 *
 * @package CampaignKit_Twitter
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'campaignkit_twitter\_%';" );

wp_cache_flush();
