<?php
/**
 * Main CampaignKitTwitter\Tweets class
 *
 * Modified version of https://wordpress.org/plugins/get-tweets-in-php/
 *
 * @package CampaginKit_Twitter
 */

namespace CampaignKitTwitter;

class Tweets {

	/**
	 * [get_tweets_html description]
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public static function get_tweets_html( $args = array() ) {

		/** Parser default arguments */
		$defaults = Options::config();
		$args = wp_parse_args( $args, $defaults );

		/** Get Twitter user timeline */
		$tweets = self::get_tweets( $args );

		$has_profile = ( $args['show_profile'] == true ) ? ' has__tweet-profile' : '';
		$has_cover = ( $args['show_cover'] == true ) ? ' has__tweet-cover' : '';

		$html = NULL;

		$html .= sprintf( '<div class="campaignkit-twitter%1$s%2$s">',
			esc_attr( $has_profile ),
			esc_attr( $has_cover )
		);

			$banner_url = isset( $tweets[0]['user']['profile_banner_url'] ) ? 'style="background-image:url('. esc_url( $tweets[0]['user']['profile_banner_url'] ) .')"'  : '';

			/** Twitter Banner */
			if ( $args['show_cover'] == true ) {

				$html .= sprintf( '<div class="tweet-cover" %s>',
					$banner_url
				);

					$html .= '<div class="tweet-cover__profile">';

						$html .= sprintf( '<div class="tweet-cover__photo"><a href="%1$s"><img src="%2$s" alt="%3$s"></a></div>',
							esc_url( 'https://twitter.com/' . $tweets[0]['user']['screen_name'] ),
							esc_url( $tweets[0]['user']['profile_image_url_https'] ),
							esc_url( $tweets[0]['user']['name'] )
						);

						$html .= sprintf( '<div class="tweet-cover__username"><a href="%1$s">%2$s<span>@%3$s</span></a></div>',
							esc_url( 'https://twitter.com/' . $tweets[0]['user']['screen_name'] ),
							esc_attr( $tweets[0]['user']['name'] ),
							esc_attr( $tweets[0]['user']['screen_name'] )
						);

						$html .= sprintf( '<div class="tweet-cover__description">%s</div>',
							wpautop( wp_kses_post( $tweets[0]['user']['description'] ) )
						);

					$html .= '</div>';

					$html .= '<div class="tweet-cover__meta">';

						$html .= sprintf( '<div class="tweet-cover__tweets"><span>%1$s</span>%2$s</div>',
							esc_html__( 'Tweets', 'campaign-kit-twitter-widget' ),
							absint( $tweets[0]['user']['statuses_count'] )
						);

						$html .= sprintf( '<div class="tweet-cover__following"><span>%1$s</span>%2$s</div>',
							esc_html__( 'Following', 'campaign-kit-twitter-widget' ),
							absint( $tweets[0]['user']['friends_count'] )
						);

						$html .= sprintf( '<div class="tweet-cover__follower"><span>%1$s</span>%2$s</div>',
							esc_html__( 'Follower', 'campaign-kit-twitter-widget' ),
							absint( $tweets[0]['user']['followers_count'] )
						);

					$html .= '</div>';

				$html .= '</div><!-- .tweet-cover -->';

			}

			/** Twitter Tweet */
			if ( $args['show_tweets'] == true ) {

				$max_height = ( $args['max_height'] != 0 ) ? ' style="max-height:'. (int)$args['max_height'] .'px"' : '';
				$html .= sprintf( '<div class="twitter-tweets" %s>', $max_height );

				foreach ( $tweets as $tweet ) {

					$html .= '<div class="twitter-tweet">';

						/** Tweet profile */
						if ( $args['show_profile'] == true ) {

							$html .= '<div class="tweet-profile">';

								$html .= sprintf( '<a href="%1$s" class="tweet-profile__image" rel="nofollow"><img src="%2$s" alt="%3$s"></a>',
									esc_url( 'https://twitter.com/' . esc_attr( $tweet['user']['screen_name'] ) ),
									esc_url( $tweet['user']['profile_image_url_https'] ),
									esc_attr( $tweet['user']['name'] )
								);

								$html .= sprintf( '<a href="%1$s" class="tweet-profile__user" rel="nofollow"><span class="tweet-profile__name">%2$s</span><span class="tweet-profile__screen">@%3$s</span></a>',
									esc_url( 'https://twitter.com/' . esc_attr( $tweet['user']['screen_name'] ) ),
									esc_attr( $tweet['user']['name'] ),
									esc_attr( $tweet['user']['screen_name'] )
								);

							$html .= '</div>';
						}

						/** Tweet text html */
						$html .= '<div class="tweet-text">'. wp_kses_post( $tweet['ck_html_text'] ) .'</div>';

						/** Tweet media image */
						if ( ( $args['show_media'] == true ) && isset( $tweet['ck_media_photo_urls'] ) ) {

							$html .= sprintf( '<div class="tweet-media tweet-media-%s">',
								count( $tweet['ck_media_photo_urls'] )
							);

							$count = 0;
							foreach ( $tweet['ck_media_photo_urls'] as $image ) {
								$count++;
								$html .= sprintf( '<figure class="tweet-media__item image-%1$s"><a href="%2$s" target="_blank"><img src="%3$s" alt=""></a></figure>',
									absint( $count ),
									esc_url( sprintf( 'https://twitter.com/%1$s/status/%2$s/photo/1', esc_attr( $tweet['user']['screen_name'] ), $tweet['id'] ) ),
									str_replace( '.jpg', '?format=jpg&name=medium', esc_url( $image ) ) //?format=jpg&name=thumb
								);
							}

							$html .= '</div>';

						}

						/** Tweet date */
						$html .= sprintf( '<a href="%1$s" class="tweet-date" target="_blank"><time datetime="%2$s">%3$s</time></a>',
							esc_url( sprintf( 'https://twitter.com/%1$s/status/%2$s', esc_attr( $tweet['user']['screen_name'] ), $tweet['id'] ) ),
							date_i18n( 'Y-m-d H:i:sO', strtotime( $tweet['created_at'] ) ),
							esc_attr( $tweet['ck_created_at'] )
						);

						/** Tweet meta */
						if ( $args['show_meta'] == true ) {

							$html .= '<div class="tweet-meta">';

								$html .= sprintf( '<a href="%1$s" class="tweet-meta__reply" title="%2$s" target="_blank"><i class="ck-twitter-icon-reply"></i></a>',
									esc_url( 'https://twitter.com/intent/tweet?in_reply_to=' . $tweet['id'] ),
									esc_html__( 'Reply', 'campaign-kit-twitter-widget' )
								);

								$html .= sprintf( '<a href="%1$s" class="tweet-meta__retweet" title="%2$s" target="_blank"><i class="ck-twitter-icon-retweet"></i> %3$s</a>',
									esc_url( 'https://twitter.com/intent/retweet?tweet_id=' . $tweet['id'] ),
									esc_html__( 'Retweet', 'campaign-kit-twitter-widget' ),
									absint( $tweet['retweet_count'] )
								);

								$html .= sprintf( '<a href="%1$s" class="tweet-meta__favorite" title="%2$s" target="_blank"><i class="ck-twitter-icon-heart"></i> %3$s</a>',
									esc_url( 'https://twitter.com/intent/favorite?tweet_id=' . $tweet['id'] ),
									esc_html__( 'Like', 'campaign-kit-twitter-widget' ),
									absint( $tweet['favorite_count'] )
								);

							$html .= '</div>';
						}

					$html .= '</div>';

				}

				$html .= '</div><!-- .twitter-tweets -->';

			}

			if ( isset( $args['show_follow'] ) ) {
				$html .= sprintf( '<a href="%1$s" class="tweet-follow" target="_blank"><i class="ck-twitter-icon-twitter"></i>%2$s</a>',
					esc_url( 'https://twitter.com/intent/follow?screen_name=' ) . esc_attr( $args['screen_name'] ),
					esc_attr( $args['follow_text'] )
				);
			}

		$html .= '</div><!-- .campaignkit-twitter -->';

		return $html;

	}

	/**
	 * [get_tweets description]
	 * @param  array  $args [description]
	 * @return array       [description]
	 */
	public static function get_tweets( $args = array() ) {

		$defaults = Options::config();

		$args = wp_parse_args( $args, $defaults );

		$config = [
			'screen_name' 		=> $args['screen_name'],
			'count'				=> $args['count'],
			'exclude_replies'	=> $args['exclude_replies'],
			'include_rts'		=> $args['include_rts'],
			'tweet_mode'		=> $args['tweet_mode'],
		];

		$twitter = self::get_user_timeline(
			$config,
			$args['cache_enabled'],
			absint( $args['cache_expiration'] )
		);

		$tweets = [];

		/** Build Tweets */
		foreach ( $twitter as $user_tweet ) {

			$tweet = $user_tweet;

			$tweet['ck_html_text'] = self::format_tweet_text(
				$user_tweet,
				$args
			);

			/** Media */

			if ( isset( $tweet['extended_entities']['media'] ) ) {

				$tweet['ck_media_photo_urls'] = [];

				foreach ( $tweet['extended_entities']['media'] as $media ) {

					if ( isset( $media['type'] ) ) {

						if ( $media['type'] != 'photo' ) {
							continue;
						}

						$tweet['ck_media_photo_urls'][] = $media['media_url_https'];

					}

				}

			}

			$tweet['ck_created_at'] = esc_html( self::relative_date( strtotime( $tweet['created_at'] ) ) );

			$tweets[] = $tweet;

		}

		return $tweets;

	}

	/**
	 * Get original user timeline from the Twitter API
	 *
	 * @param  array   $args             [description]
	 * @param  boolean $cache_enabled    [description]
	 * @param  integer $cache_expiration [description]
	 * @return [type]                    [description]
	 */
	private static function get_user_timeline( $args = array(), $cache_enabled = true, $cache_expiration = 5 ) {

		$defaults = [
			'screen_name' 		=> 'campaignkitau',
			'count'				=> 5,
			'exclude_replies'	=> false,
			'include_rts'		=> true,
			'tweet_mode'		=> 'extended'
		];

		$args = wp_parse_args( $args, $defaults );

		$prefix 		= str_replace( '-', '_', sanitize_title_with_dashes( CAMPAIGNKIT_TWITTER_TEXT_DOMAIN ) );
		$transient_name = $prefix . '_' . $args['screen_name'] . '_' . $args['count'] . '_' . $args['exclude_replies'] . '_' . $args['include_rts'] . '_' . $args['tweet_mode'];

		$user_timeline = null;

		if ( $cache_enabled ) {

			$user_timeline = get_transient( $transient_name );

			if ( $user_timeline !== false ) {
				return $user_timeline;
			}

		}

		/** Retrive user timeline */
		$keys 			= Options::keys();
		$twitter_api 	= new \TwitterAPIExchange( $keys );
		$url 			= 'https://api.twitter.com/1.1/statuses/user_timeline.json';
		$requestMethod 	= 'GET';
		$get_fields 	= http_build_query( $args );

		$user_timeline = $twitter_api
			->setGetfield( '?' . $get_fields )
			->buildOauth( $url, $requestMethod )
			->performRequest()
		;

		$user_timeline = json_decode( $user_timeline, true );

		if ( $cache_enabled ) {
			/** Save in the cache if enabled */
			set_transient( $transient_name, $user_timeline, $cache_expiration * MINUTE_IN_SECONDS );
		} else {
			/** Clear the cache if disabled */
			delete_transient( $transient_name );
		}

		return $user_timeline;

	}

	/**
	 * Set a default value for the given key.
	 *
	 * @param [type] &$array  [description]
	 * @param [type] $key     [description]
	 * @param [type] $default [description]
	 */
	private static function set_default( &$array, $key, $default ) {

		if ( ! isset ( $array[$key] ) ) {
			$array[$key] = $default;
		}
		return;

	}

	/**
	 * [format_tweet_text description]
	 * @param  [type] $tweet [description]
	 * @param  array  $args  [description]
	 * @return [type]        [description]
	 */
	private static function format_tweet_text( $tweet, $args = array() ) {

		/** Set up config */

		self::set_default( $args,
			'hashtag_link_template',
			'<a href="{{hashtag_link}}" rel="nofollow" target="_blank">#{{hashtag_text}}</a>'
		);

		self::set_default( $args,
			'ctag_link_template',
			'<a href="{{ctag_link}}" rel="nofollow" target="_blank">${{ctag_text}}</a>'
		);

		self::set_default( $args,
			'url_link_template',
			'<a href="{{url_link}}" rel="nofollow" target="_blank" title="{{url_title}}">{{url_text}}</a>'
		);

		self::set_default( $args,
			'user_mention_link_template',
			'<a href="{{user_mention_link}}" rel="nofollow" target="_blank" title="{{user_mention_title}}">@{{user_mention_text}}</a>'
		);

		self::set_default( $args,
			'media_link_template',
			'<a href="{{media_link}}" rel="nofollow" target="_blank" title="{{media_title}}">{{media_text}}</a>'
		);

		// Is retweeted?
		if ( isset( $tweet['retweeted_status'] ) ) {
			$retweeted_status = self::parse_tweet_text( $tweet['retweeted_status'], $args );
			return '<span class="retweet-badge">' . esc_html( 'Retweet', 'campaign-kit-twitter-widget' ) . ' <i class="ck-twitter-icon-retweet"></i></span> ' . $retweeted_status;
		}

		// Is Quotes?
		if ( isset( $tweet['quoted_status'] ) ) {
			$quoted_status = self::parse_tweet_text( $tweet['quoted_status'], $args );
			return $quoted_status;
		}

		/** Return the parsed tweet */
		return self::parse_tweet_text( $tweet, $args );

	}

	/**
	 * [parse_tweet_text description]
	 * @param  [type] $tweet [description]
	 * @param  array  $args  [description]
	 * @return [type]        [description]
	 */
	private static function parse_tweet_text( $tweet, $args = array() ) {

		/** Collects the set of entities */
		$entity_holder = array();

		/** Hashtags */
		if ( isset( $tweet['entities']['hashtags'] ) ) {

			$template = $args['hashtag_link_template'];

			foreach ( $tweet['entities']['hashtags'] as $hashtag ) {

				// Link: https://twitter.com/hashtag/{{1}}?src=hash
				$hashtag_link = str_replace( '{{1}}', strtolower( $hashtag['text'] ), 'https://twitter.com/hashtag/{{1}}?src=hash' );

				$replace = str_replace( '{{hashtag_link}}', $hashtag_link, $template );

				$replace = str_replace( '{{hashtag_text}}', $hashtag['text'], $replace );

				self::add_entity( $entity_holder, $hashtag, $replace );

			}

		}

		/** ctags */
		if ( isset( $tweet['entities']['symbols'] ) ) {

			$template = $args['ctag_link_template'];

			foreach ( $tweet['entities']['symbols'] as $symbol ) {

				// Link: https://twitter.com/search?q=%24{{1}}&src=ctag
				$symbol_link = str_replace( '{{1}}', strtolower( $symbol['text'] ), 'https://twitter.com/search?q=%24{{1}}&src=ctag' );

				$replace = str_replace( '{{ctag_link}}', $symbol_link, $template );

				$replace = str_replace( '{{ctag_text}}', $symbol['text'], $replace );

				self::add_entity( $entity_holder, $symbol, $replace );

			}

		}

		/** User mentions */
		if ( isset( $tweet['entities']['user_mentions'] ) ) {

			$template = $args['user_mention_link_template'];

			foreach ( $tweet['entities']['user_mentions'] as $user_mention ) {

				// Link: https://twitter.com/{{1}}
				$user_mention_link = str_replace( '{{1}}', strtolower( $user_mention['screen_name'] ), 'https://twitter.com/{{1}}' );

				$replace = str_replace( '{{user_mention_link}}', $user_mention_link, $template );
				$replace = str_replace( '{{user_mention_title}}', $user_mention['name'], $replace );
				$replace = str_replace( '{{user_mention_text}}', $user_mention['screen_name'], $replace );

				self::add_entity( $entity_holder, $user_mention, $replace );

			}

		}

		/** Urls */
		if ( isset( $tweet['entities']['urls'] ) ) {

			$template = $args['url_link_template'];

			foreach ( $tweet['entities']['urls'] as $url ) {

				$replace = str_replace( '{{url_link}}', $url['url'], $template );

				$replace = str_replace( '{{url_title}}', $url['expanded_url'], $replace );

				$replace = str_replace( '{{url_text}}', $url['display_url'], $replace );

				self::add_entity( $entity_holder, $url, $replace );

			}

		}

		/** Media Link */
		if ( isset( $tweet['entities']['media'] ) ) {

			$template = $args['media_link_template'];

			foreach ( $tweet['entities']['media'] as $media ) {

				$replace = str_replace( '{{media_link}}', $media['url'], $template );
				$replace = str_replace( '{{media_title}}', $media['expanded_url'], $replace );
				$replace = str_replace( '{{media_text}}', $media['display_url'], $replace );

				self::add_entity( $entity_holder, $media, $replace );

			}

		}

		// Sort the entities in reverse order by their starting index
		krsort( $entity_holder );

		// Replace the tweet's text with the entities
		if ( $args['tweet_mode'] === 'extended' ) {
			$text = $tweet['full_text'];
		} elseif( $args['tweet_mode'] === 'compat' ) {
			$text = $tweet['text'];
		}

		foreach ( $entity_holder as $entity ) {
			$text = self::mb_substr_replace( $text, $entity->replace, $entity->start, $entity->length, 'utf-8' );
		}

		return $text;

	}

	/**
	 * Add an entity to the entity_holder.
	 *
	 * @param [type] &$entity_holder [description]
	 * @param [type] $tweet_entity   [description]
	 * @param [type] $replace        [description]
	 */
	private static function add_entity( &$entity_holder, $tweet_entity, $replace ) {

		$entity 						= new \stdClass();
		$entity->start 					= $tweet_entity['indices'][0];
		$entity->end 					= $tweet_entity['indices'][1];
		$entity->length 				= $entity->end - $entity->start;
		$entity->replace 				= $replace;
		$entity_holder[$entity->start] 	= $entity;

		return;

	}

  	/**
  	 * String replacement supporting UTF-8 encoding.
  	 *
  	 * @param  [type] $string      [description]
  	 * @param  [type] $replacement [description]
  	 * @param  [type] $start       [description]
  	 * @param  [type] $length      [description]
  	 * @param  [type] $encoding    [description]
  	 * @return [type]              [description]
  	 */
	private static function mb_substr_replace( $string, $replacement, $start, $length = null, $encoding = null ) {

		$strlen 		= mb_strlen( $string, $encoding );
		$first_piece 	= mb_substr( $string, 0, $start, $encoding ) . $replacement;
		$second_piece 	= '';

		if ( isset( $length ) ) {
			$second_piece = mb_substr( $string, $start + $length, $strlen, $encoding );
		}

		return $first_piece . $second_piece;

	}

	/**
	 * Utility converts the date [of a tweet] to relative time descriprion, e.g. about 2 minutes ago
	 *
	 * Modified version of https://wordpress.org/plugins/latest-tweets-widget
	 *
	 * @param  [type] $strdate [description]
	 * @return [type]          [description]
	 */
	private static function relative_date( $strdate ) {

	    // get universal time now.
	    static $t, $y, $m, $d, $h, $i, $s, $o;
	    if( ! isset( $t ) ){
	        $t = time();
	        sscanf( gmdate('Y m d H i s',$t), '%u %u %u %u %u %u', $y,$m,$d,$h,$i,$s );
	    }

	    // get universal time of tweet
	    $tt = is_int( $strdate ) ? $strdate : strtotime( $strdate );
	    if( ! $tt || $tt > $t ){
	        // slight difference between our clock and Twitter's clock can cause problem here - just pretend it was zero seconds ago
	        $tt = $t;
	        $tdiff = 0;
	    } else {
	        sscanf( gmdate( 'Y m d H i s', $tt ), '%u %u %u %u %u %u', $yy,$mm,$dd,$hh,$ii,$ss );
	        // Calculate relative date string
	        $tdiff = $t - $tt;
	    }

	    // Less than a minute ago?
	    if( $tdiff < 60 ){
	        return __( 'Just now', 'campaign-kit-twitter-widget' );
	    }

	    // within last hour? X minutes ago
	    if( $tdiff < 3600 ){
	        $idiff = (int) floor( $tdiff / 60 );
	        return sprintf( _n( '%u minute ago', '%u minutes  ago', $idiff, 'campaign-kit-twitter-widget' ), $idiff );
	    }

	    // within same day? About X hours ago
	    $samey = ( $y === $yy ) and
	    $samem = ( $m === $mm ) and
	    $samed = ( $d === $dd );
	    if( ! empty( $samed ) ){
	        $hdiff = (int) floor( $tdiff / 3600 );
	        return sprintf( _n( 'About an hour ago', 'About %u hours  ago', $hdiff, 'campaign-kit-twitter-widget' ), $hdiff );
	    }
	    $tf = get_option( 'time_format' ) or $tf = 'g:i A';

	    // within 24 hours?
	    if( $tdiff < 86400 ){
	        return __( 'Yesterday at', 'campaign-kit-twitter-widget' ).date_i18n( ' '. $tf, $tt );
	    }

	    // else return formatted date, e.g. "Oct 20th 2008 9:27 PM" */
	    $df = get_option( 'date_format' ) or $df= 'M jS Y';

	    return date_i18n( $df . ' - ' . $tf, $tt );

	}

}
