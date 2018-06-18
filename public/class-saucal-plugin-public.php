<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.jonnyrudd.co.uk
 * @since      1.0.0
 *
 * @package    Saucal_Plugin
 * @subpackage Saucal_Plugin/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Saucal_Plugin
 * @subpackage Saucal_Plugin/public
 * @author     Jonny Rudd <jonny@jonnyrudd.co.uk>
 */
class Saucal_Plugin_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Saucal_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Saucal_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/saucal-plugin-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Saucal_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Saucal_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/saucal-plugin-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add the Data Feed.
	 *
	 * @param      array $menu_links  The menu links.
	 *
	 * @return     array  The menu links.
	 */
	function data_feed_link( $menu_links ) {

		$menu_links = array_slice( $menu_links, 0, 5, true )
		+ array( 'data-feed' => 'Data Feed' )
		+ array_slice( $menu_links, 5, null, true );

		return $menu_links;

	}
	/**
	 * Add the data feed options.
	 *
	 * @param      array $menu_links  The menu links.
	 *
	 * @return     array  The menu links.
	 */
	function data_feed_options_link( $menu_links ) {

		$menu_links = array_slice( $menu_links, 0, 6, true )
		+ array( 'data-feed-options' => 'Data Feed Settings' )
		+ array_slice( $menu_links, 5, null, true );

		return $menu_links;

	}

	/**
	 * Register the endpoints.
	 */
	function my_account_new_endpoints() {
		 add_rewrite_endpoint( 'data-feed', EP_ROOT | EP_PAGES );
		 add_rewrite_endpoint( 'data-feed-options', EP_ROOT | EP_PAGES );
	}

	/**
	 * Data Feed Content.
	 */
	function my_account_endpoint_content() {

		// Print out some content from the feed.
		$this->data_feed_content();

	}

	/**
	 * Data feed content creation. From wither API or transient.
	 */
	function data_feed_content() {

		$this->display_settings_updated();

		// Get the data set if it's available in a transient.
		$data = $this->check_if_transient_set();

		// If we don't have a transient then we need to do the API call.
		if ( empty( $data ) ) {

			// Let's get our data via an endpoint.
			$request = wp_remote_get( 'https://pippinsplugins.com/edd-api/products' );

			// Make sure there is no error.
			if ( is_wp_error( $request ) ) {
				// If there is an error we need to drop out of this code block to prevent further errors.
				// @todo create a fallback.
				return false;
			}
			// We got some data back, now let's get rid of the stuff we don't need (anything but the body).
			$body = wp_remote_retrieve_body( $request );
			// Now in English please!
			$data = json_decode( $body );

			// Get our users update preferences so we can set the right transient.
			$feed_update_preferences = $this->get_update_preferences();

			// Update the transient.
			$this->set_transient( $data, $feed_update_preferences );

		}

		// Just incase it is a succesful call but empty.
		if ( ! empty( $data ) ) {
			// Let's display the data.
			echo '<ul>';
			foreach ( $data->products as $product ) {
				echo '<li>';
				echo '<a href="' . esc_url( $product->info->link ) . '">' . $product->info->title . '</a>';
				echo '</li>';
			}
			echo '</ul>';
		}
	}

	/**
	 * Data Feed Settings form.
	 */
	function data_feed_settings_endpoint_content() {

	?>
	<form name="feed_update" action="" method="POST">
		<select name="feed_update">
			<option value="" disabled selected idden><?php _e( 'Please Select', 'saucal-plugin' ); ?></option>
			<option value="Hourly">Hourly</option>
			<option value="Daily">Daily</option>
			<option value="Weekly">Weekly</option>
			<option value="Monthly">Monthly</option>
		</select>
		<button type="submit"><?php _e( 'Update', 'saucal-plugin' ); ?></button>
	</form>

<?php
	}

	/**
	 * Redirect to feed after settings have been updated/submitted front end.
	 */
	function redirect_on_feed_settings_update() {
		if ( isset( $_POST['feed_update'] ) ) {
			wp_safe_redirect( home_url( 'my-account/data-feed/?options-updated=' . $_POST['feed_update'] ) );
			exit;
		}
	}

	/**
	 * Display how regular the feed is set to update within the users account.
	 */
	function display_settings_updated() {
		$settings_updated_to = isset( $_GET['options-updated'] ) ? $_GET['options-updated'] : '';
		if ( ! empty( $settings_updated_to ) ) {
			echo '<div class="feed-display-message"><p><strong>' . sprintf( esc_html__( 'You feed is currently set to update %s.', 'saucal-plugin' ), $settings_updated_to ) . '</strong></p></div>';
			// Get the current user ID.
			$user_id = get_current_user_id();
			update_user_meta( $user_id, '_feed_update', $settings_updated_to );
		} else {
			$settings_updated_to = $this->get_update_preferences();
			echo '<div class="feed-display-message"><p><strong>' . sprintf( esc_html__( 'You feed is currently set to update %s.', 'saucal-plugin' ), $settings_updated_to ) . '</strong></p></div>';
		}
	}

	/**
	 * Sets the transient.
	 *
	 * @param      <type> $data                     The data.
	 * @param      <type> $feed_update_preferences  The feed update preferences.
	 */
	function set_transient( $data, $feed_update_preferences ) {
		if ( 'Hourly' === $feed_update_preferences ) {
			set_transient( 'feed_update_hourly', $data, HOUR_IN_SECONDS );
		} elseif ( 'Daily' === $feed_update_preferences ) {
			set_transient( 'feed_update_daily', $data, DAY_IN_SECONDS );
		} elseif ( 'Weekly' === $feed_update_preferences ) {
			set_transient( 'feed_update_weekly', $data, WEEK_IN_SECONDS );
		} else {
			set_transient( 'feed_update_monthly', $data, MONTH_IN_SECONDS );
		}
		return;
	}

	/**
	 * Get the users update preferences.
	 *
	 * @return     <type>  The update preferences.
	 */
	function get_update_preferences() {
		// Get the current user ID.
		$user_id = get_current_user_id();
		// Get there update preferences based on meta value.
		$feed_update_preferences = get_user_meta( $user_id, '_feed_update', true );
		// If null, set it.
		if ( empty( $feed_update_preferences ) ) {
			update_user_meta( $user_id, '_feed_update', 'Hourly' );
			$feed_update_preferences = 'Hourly';
		}
		// Return the preferences.
		return $feed_update_preferences;
	}

	/**
	 * Check if a transient is set.
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function check_if_transient_set() {
		// Get the users update preferences so we are setting the right transient.
		$feed_update_preferences = $this->get_update_preferences();
		// Hourly.
		if ( 'Hourly' === $feed_update_preferences ) {
			$data = get_transient( 'feed_update_hourly' );
			// Daily.
		} elseif ( 'Daily' === $feed_update_preferences ) {
			$data = get_transient( 'feed_update_daily' );
			// Weekly.
		} elseif ( 'Weekly' === $feed_update_preferences ) {
			$data = get_transient( 'feed_update_weekly' );
			// Anything else (Monthly).
		} else {
			$data = get_transient( 'feed_update_monthly' );
		}
		// Check if there is some data.
		if ( ! empty( $data ) ) {
			// Return it.
			return $data;
		} else {
			return;
		}
	}
}
