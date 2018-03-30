/* global ajaxurl, CampaignKitTwitter */
( function( wp, $ ) {
	'use strict';

	if ( ! wp ) {
		return;
	}

	$( function() {
		// Dismiss notice
		$( document ).on( 'click', '.campaignkit-twitter-notice', function() {
			$.ajax({
				type:     'POST',
				url:      ajaxurl,
				data:     { nonce: CampaignKitTwitter.nonce, action: 'campaignkit_twitter_dismiss_notice' },
				dataType: 'json'
			});
		});
	});
})( window.wp, jQuery );
