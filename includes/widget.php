<?php
/**
 * Widget Class
 *
 * @package CampaignKit_Twitter
 */

namespace CampaignKitTwitter;

class Widget extends \WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor. Set the default widget options and create widget.
	 */
	public function __construct() {

		$config = Options::config();

		$this->defaults = [
			'title'				=> $config['title'],
			'screen_name' 		=> $config['screen_name'],
			'count'				=> $config['count'],
			'cache_enabled'		=> $config['cache_enabled'],
			'cache_expiration'	=> $config['cache_expiration'],
			'max_height'		=> $config['max_height'],
			'show_cover'		=> $config['show_cover'],
			'show_tweets'		=> $config['show_tweets'],
			'show_profile'		=> $config['show_profile'],
			'show_media'		=> $config['show_media'],
			'show_meta'			=> $config['show_meta'],
			'show_follow'		=> $config['show_follow'],
			'follow_text'		=> $config['follow_text'],
		];

		$widget_ops = array(
			'classname'   => 'campaign-kit-twitter-widget',
			'description' => esc_html__( 'Displays Twitter timeline block.', 'campaign-kit-twitter-widget' ),
			'customize_selective_refresh' => true,
		);

		$control_ops = array(
			'id_base' => 'campaign-kit-twitter-widget',
			'width'   => 200,
			'height'  => 250,
		);

		parent::__construct(
			'campaign-kit-twitter-widget',
			esc_html__( 'Campaign Kit - Twitter', 'campaign-kit-twitter-widget' ),
			$widget_ops,
			$control_ops
		);

	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $args['before_widget'];

		// Set up the author bio.
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title']; // WPCS: prefix ok.
		}

		$config = [
			'title'				=> $instance['title'],
			'screen_name' 		=> $instance['screen_name'],
			'count'				=> $instance['count'],
			'cache_enabled'		=> $instance['cache_enabled'],
			'cache_expiration'	=> $instance['cache_expiration'],
			'max_height'		=> $instance['max_height'],
			'show_cover'		=> $instance['show_cover'],
			'show_tweets'		=> $instance['show_tweets'],
			'show_profile'		=> $instance['show_profile'],
			'show_media'		=> $instance['show_media'],
			'show_meta'			=> $instance['show_meta'],
			'show_follow'		=> $instance['show_follow'],
			'follow_text'		=> $instance['follow_text'],
		];

		echo Tweets::get_tweets_html( $config );

		echo $args['after_widget'];

	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'campaign-kit-twitter-widget' ); ?>:</label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'screen_name' ) ); ?>"><?php _e( 'Twitter Username', 'campaign-kit-twitter-widget' ); ?>:</label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'screen_name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'screen_name' ) ); ?>" value="<?php echo esc_attr( $instance['screen_name'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php _e( 'Number of tweets to display', 'campaign-kit-twitter-widget' ); ?>:</label>
			<input type="number" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" value="<?php echo esc_attr( $instance['count'] ); ?>" class="alignright tiny-text" step="1" min="1" size="4" />
		</p>


		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_cover' ) ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_cover' ) ); ?>" value="1" <?php checked( $instance['show_cover'] ); ?>/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_cover' ) ); ?>"><?php _e( 'Show user banner', 'campaign-kit-twitter-widget' ); ?></label>
		</p>

		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_tweets' ) ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_tweets' ) ); ?>" value="1" <?php checked( $instance['show_tweets'] ); ?>/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_tweets' ) ); ?>"><?php _e( 'Show user tweets', 'campaign-kit-twitter-widget' ); ?></label>
		</p>

		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_profile' ) ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_profile' ) ); ?>" value="1" <?php checked( $instance['show_profile'] ); ?>/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_profile' ) ); ?>"><?php _e( 'Show user photo on each tweet', 'campaign-kit-twitter-widget' ); ?></label>
		</p>

		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_media' ) ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_media' ) ); ?>" value="1" <?php checked( $instance['show_media'] ); ?>/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_media' ) ); ?>"><?php _e( 'Show media photos', 'campaign-kit-twitter-widget' ); ?></label>
		</p>

		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_meta' ) ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_meta' ) ); ?>" value="1" <?php checked( $instance['show_meta'] ); ?>/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_meta' ) ); ?>"><?php _e( 'Show Reply/Retweet/Like', 'campaign-kit-twitter-widget' ); ?></label>
		</p>

		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_follow' ) ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_follow' ) ); ?>" value="1" <?php checked( $instance['show_follow'] ); ?>/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_follow' ) ); ?>"><?php _e( 'Show follow button', 'campaign-kit-twitter-widget' ); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'follow_text' ) ); ?>"><?php _e( 'Follow button text', 'campaign-kit-twitter-widget' ); ?>:</label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'follow_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'follow_text' ) ); ?>" value="<?php echo esc_attr( $instance['follow_text'] ); ?>" class="widefat" />
		</p>

		<hr class="div" />

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'max_height' ) ); ?>"><?php _e( 'Max tweet container height', 'campaign-kit-twitter-widget' ); ?>:</label>
			<input type="number" id="<?php echo esc_attr( $this->get_field_id( 'max_height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'max_height' ) ); ?>" value="<?php echo esc_attr( $instance['max_height'] ); ?>" class="alignright small-text" step="1" min="0" size="4" />
		</p>

		<hr class="div" />

		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'cache_enabled' ) ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'cache_enabled' ) ); ?>" value="1" <?php checked( $instance['cache_enabled'] ); ?>/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'cache_enabled' ) ); ?>"><?php _e( 'Enable cache', 'campaign-kit-twitter-widget' ); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'cache_expiration' ) ); ?>"><?php _e( 'Cache expiration (in minutes)', 'campaign-kit-twitter-widget' ); ?>:</label>
			<input type="number" id="<?php echo esc_attr( $this->get_field_id( 'cache_expiration' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cache_expiration' ) ); ?>" value="<?php echo esc_attr( $instance['cache_expiration'] ); ?>" class="small-text" step="1" min="1" size="3" />
		</p>

		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$new_instance['title']				= strip_tags( $new_instance['title'] );
		$new_instance['screen_name']		= strip_tags( $new_instance['screen_name'] );
		$new_instance['count']				= (int) $new_instance['count'];
		$new_instance['cache_enabled']		= (bool) $new_instance['cache_enabled'];
		$new_instance['cache_expiration']	= (int) $new_instance['cache_expiration'];
		$new_instance['max_height']			= (int) $new_instance['max_height'];
		$new_instance['show_cover']			= (bool) $new_instance['show_cover'];
		$new_instance['show_tweets']		= (bool) $new_instance['show_tweets'];
		$new_instance['show_profile']		= (bool) $new_instance['show_profile'];
		$new_instance['show_media']			= (bool) $new_instance['show_media'];
		$new_instance['show_meta']			= (bool) $new_instance['show_meta'];
		$new_instance['show_follow']		= (bool) $new_instance['show_follow'];
		$new_instance['follow_text']		= strip_tags( $new_instance['follow_text'] );

		return $new_instance;

	}

}
