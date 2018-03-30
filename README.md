# Twitter Widget by Campaign Kit #
**Contributors:** [elevate360](https://profiles.wordpress.org/elevate360), [campaignkit](https://profiles.wordpress.org/campaignkit)  
**Donate link:** https://campaignkit.co/  
**Tags:** twitter, tweet, tweets, latest-tweets, oauth, api, rest, api, widget, sidebar, shortcode  
**Requires at least:** 4.5  
**Tested up to:** 4.9  
**Requires PHP:** 5.4  
**Stable tag:** 1.0.0  
**License:** GNU General Public License v2.0 (or later)  
**License URI:** http://www.opensource.org/licenses/gpl-license.php  

A lightweight and stylish widget to display your latest Tweet for your WordPress site.

## Description ##

Looking for the easiest way to display tweets on your WordPress site? The ***Campaign Kit Twitter widget*** allows you to display a stream of your recent tweets, with images hash and cash tags and a link to the original tweet. You can also display the Twitter account banner and a follow me link.

Easy to use by using widget, function or shortcode. Flexible and extendable.

### Features ###

Parse data from the Twitter API:

* Support hashtags link
* Support ctag link
* User mention link
* User link
* User media link
* Cache request

### Credits ###

Twitter Widget by Campaign Kit bundles the following third-party resources:

* [Twitter API Wrapper](https://github.com/j7mbo/twitter-api-php) **License:** MIT
* Icons by [Fontawesome 5](https://fontawesome.com/) and generated by [Iconmoon](https://icomoon.io) **License:** SIL OFL 1.1

Code inspired by [Get Tweets in PHP](https://wordpress.org/plugins/get-tweets-in-php) and [Latest Tweets Widget](https://wordpress.org/plugins/latest-tweets-widget).

## Installation ##

### Minimum Requirements ###

* PHP version 5.4 or higher
* cURL
* Twitter API key. You can get API keys from [Twitter application dashboard](https://dev.twitter.com/apps).

### Automatic installation ###

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of the plugin, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “Twitter Widget by Campaign Kit” and click Search Plugins. Once you’ve found our plugin you can view details about it such as the point release, rating, and description. Most importantly, of course, you can install it by simply clicking “Install Now” and click “Activate”.

### Manual installation ###

The manual installation method involves downloading our Twitter plugin and uploading it to your web server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

### Configure Twitter API ###

1. Go to the [My applications](https://dev.twitter.com/apps) page on the Twitter website to set up your website as a new Twitter application. You may need to log-in using your Twitter username and password.
2. If you don't already have a suitable application that you can use for your website, set one up on the Create an Application page.
3. After clicking Create your Twitter application, on the following page, click on Create my access token.
4. Copy the **Consumer key**, **Consumer secret**, **Access token** and **Access token secret** from your Twitter application page into Campaign Kit Twitter Admin Setting.
5. Click on **Save Changes**.

### Using Widget ###

* Drag and drop **Campaign Kit - Twitter** widget into the available sidebar.
* Customize the settings to your liking.


### Using Shortcode ###

You can insert Twitter feed into your post by using a shortcode:

	[ck_tweets]

Shortcode parameter:

* `screen_name` - [string | default **campaignkitau**] The screen name of the user for whom to return results.
* `count` - [integer | default **5**] Specifies the number of Tweets to try and retrieve, up to a maximum of 200 per distinct request. The value of count is best thought of as a limit to the number of Tweets to return because suspended or deleted content is removed after the count has been applied. We include retweets in the count, even if include_rts is not supplied.
* `exclude_replies` - [boolean | default **false**] This parameter will prevent replies from appearing in the returned timeline. Using exclude_replies with the count parameter will mean you will receive up-to count tweets — this is because the count parameter retrieves that many Tweets before filtering out retweets and replies.
* `include_rts` - [boolean | default **true**] When set to false, the timeline will strip any native retweets (though they will still count toward both the maximal length of the timeline and the slice selected by the count parameter). Note: If you’re using the trim_user parameter in conjunction with include_rts, the retweets will still contain a full user object.
* `cache_enabled` - [boolean | default **true**] Save latest tweets into WordPress transient to avoid Twitter API request limiter.
* `cache_expiration` - [integer | default **5**] How long does the cache will expire within minutes.
* `max_height` - [integer | default **0**] Auto scrollable tweets area. Set to `0` will remove the maximum height.
* `show_cover` - [boolean | default **true**] Display selected Twitter background cover, profile picture, number of total tweets, following and follower.
* `show_tweets` - [boolean | default **true**] Display your latest tweets if set to true.
* `show_profile` - [boolean | default **true**] Display Twitter profile picture on each tweet if set to true.
* `show_media` - [boolean | default **true**] Display any embedded photos if set to true.
* `show_meta` - [boolean | default **true**] Display a reply, retweet and like buttons if set to true.
* `show_follow` - [boolean | default **true**] Display a follow link button if set to true.
* `follow_text` - [string | default **Follow Me**] A follow link text (e.g Follow Me)

Example usage :

	[ck_tweets screen_name="campaignkitau" count="3"]

This will display three latest tweets from [@campaignkitau](https://twitter.com/campaignkitau).

### Advanced Usage ###

For some reason, you might want to design your own Twitter markup and design to match your theme.

You can build your own twitter markup by using following code:

	campaignkit_twitter_get_tweets( $args );
	// or
	CampaignKitTwitter\Tweets:get_tweets( $args );

This function will return all the data within an array.

Example usage:

	$args = [
		'screen_name' 		=> 'campaignkitau',
		'count'				=> '3',
	];

	$tweets = campaignkit_twitter_get_tweets( $args );

	foreach( $tweets as $tweet ) {
		echo wpautop( $tweet['ck_html_text'] );
	}

	// print_r( $tweets ) to see all the complete list of Twitter API data.

We also include custom properties between the output (**not** officially from Twitter API):

* `ck_html_text` - a parsing version of Twitter text.
* `ck_media_photo_urls` - a list of media images url.
* `ck_created_at` - a human readable timestamp.

## Frequently Asked Questions ##

### What options can I use for the shortcode? ###

All the options are listed on the [plugin installation page](https://wordpress.org/plugins/campaignkit-twitter/installation/).

### How to disable style and script ###

You can simply add this function into your theme functions:

* `add_filter( 'campaignkit_twitter_load_style', '__return_false' )` to disable any styles from this plugin.
* `add_filter( 'campaignkit_twitter_load_script', '__return_false' )` to disable any scripts from this plugin.

### How to build my own Twitter markup? ###

You can learn more at [plugin installation page](https://wordpress.org/plugins/campaignkit-twitter/installation/).

## Screenshots ##

1. Campaign Kit - Twitter API settings panel.
2. Campaign Kit - Twitter widget.
3. Campaign Kit - Twitter widget display.

## Changelog ##

### 1.0.0 ###

Initial release

## Upgrade Notice ##

None
