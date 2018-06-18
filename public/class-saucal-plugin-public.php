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
	 * @param      <type> $menu_links  The menu links
	 *
	 * @return     <type>  ( description_of_the_return_value )
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
	 * @param      <type> $menu_links  The menu links
	 *
	 * @return     <type>  ( description_of_the_return_value )
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
	 * Testing Content.
	 */
	function my_account_endpoint_content() {

		// Print out some content.
		$this->testing_endpoint_content();

	}

	function testing_endpoint_content() {

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
	 * { function_description }
	 */
	function data_feed_settings_endpoint_content() {

		$user_id = get_current_user_id();
		$feed_update = get_user_meta( $user_id, '_feed_update', true );
		// @todo create some helper text in this area before the form.
	?>
	<form name="feed_update" action="" method="POST">
		<select name="feed_update">
			<option value="feed_update"><?php echo 'Please Select'; ?></option>
			<option value="Hourly">Hourly</option>
			<option value="Daily">Daily</option>
			<option value="Weekly">Weekly</option>
			<option value="Monthly">Monthly</option>
		</select>
		<button type="submit">Update</button>
	</form>

<?php
$feed_update = $_POST['feed_update'];
update_user_meta( $user_id, '_feed_update', $feed_update );
// @todo make a redirect here to go back to feed. With an success alert to show the settings have been updated.
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
		return $data;
	}
}
