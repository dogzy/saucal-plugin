<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.jonnyrudd.co.uk
 * @since      1.0.0
 *
 * @package    Saucal_Plugin
 * @subpackage Saucal_Plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Saucal_Plugin
 * @subpackage Saucal_Plugin/admin
 * @author     Jonny Rudd <jonny@jonnyrudd.co.uk>
 */
class Saucal_Plugin_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/saucal-plugin-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/saucal-plugin-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Show a message to alert the user WooCommerce is needed for this plugin. (and offer a quick link to download it).
	 */
	public function woocommerce_not_active() {

		echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'This plugin requires WooCommerce to be installed and active. You can download %s here.', 'saucal-plugin' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
	}

	/**
	 * Check for an active install of WooCommerce.
	 */
	public function check_woocommerce_installed() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', 'saucal_woocommerce_not_active' );
		}
	}

	/**
	 * Handle rewrite rules when a user switches themes.
	 */
	public function woocommerce_flush_rewrite_rules() {
		flush_rewrite_rules();
	}

	/**
	 * custom option and settings
	 */
	public function saucal_settings_init() {
		// register a new setting for "saucal-settings" page
		register_setting( 'saucal-settings', 'saucal_live_endpoint' );
		register_setting( 'saucal-settings', 'saucal_test_endpoint' );
		register_setting( 'saucal-settings', 'saucal_test_mode' );

		// register a new section in the "wporg" page
		add_settings_section(
			'saucal_section_endpoints',
			__( 'Set saucal Endpoints.', 'saucal-settings' ),
			array( $this, 'saucal_section_endpoints_cb' ),
			'saucal-settings'
		);

		// register a new field in the "saucal_section_endpoints" section, inside the "saucal-settings" page
		add_settings_field(
			'saucal_live_endpoint', // as of WP 4.6 this value is used only internally
			// use $args' label_for to populate the id inside the callback
			__( 'Live Endpoint URL', 'saucal-settings' ),
			array( $this, 'saucal_live_endpoint_cb' ),
			'saucal-settings',
			'saucal_section_endpoints',
			[
				'label_for'         => 'saucal_live_endpoint',
				'class'             => 'saucal_row regular-text',
				'saucal_custom_data' => 'custom',
			]
		);
		add_settings_field(
			'saucal_test_endpoint', // as of WP 4.6 this value is used only internally
			// use $args' label_for to populate the id inside the callback
			__( 'Test Endpoint URL', 'saucal-settings' ),
			array( $this, 'saucal_test_endpoint_cb' ),
			'saucal-settings',
			'saucal_section_endpoints',
			[
				'label_for'         => 'saucal_test_endpoint',
				'class'             => 'saucal_row regular-text',
				'saucal_custom_data' => 'custom',
			]
		);
		add_settings_field(
			'saucal_test_mode', // as of WP 4.6 this value is used only internally
			// use $args' label_for to populate the id inside the callback
			__( 'Enable saucal Test Mode', 'saucal-settings' ),
			array( $this, 'saucal_test_mode_cb' ),
			'saucal-settings',
			'saucal_section_endpoints',
			[
				'label_for'         => 'saucal_test_mode',
				'class'             => 'saucal_row',
				'saucal_custom_data' => 'custom',
			]
		);
	}

	/**
	 * custom option and settings:
	 * callback functions
	 */

	// developers section cb
	// section callbacks can accept an $args parameter, which is an array.
	// $args have the following keys defined: title, id, callback.
	// the values are defined at the add_settings_section() function.
	public function saucal_section_endpoints_cb( $args ) {
		?>
		<p id="<?php esc_attr( $args['id'] ); ?>"><?php esc_html__( 'Follow the white rabbit.', 'saucal-settings' ); ?></p>
		<?php
	}

	public function saucal_live_endpoint_cb( $args ) {
		// get the value of the setting we've registered with register_setting()
		$option = get_option( 'saucal_live_endpoint' );
		// output the field
		?>
		<input type="url"
			id="<?php esc_attr( $args['label_for'] ); ?>"
			class="<?php esc_attr( $args['class'] ); ?>"
			name="<?php esc_attr( $args['label_for'] ); ?>"
			value="
			<?php
			if ( isset( $option ) ) :
				echo $option;
endif;
?>
" />

		<p class="description">
			<?php esc_html( 'Enter your live API endpoint here.', 'saucal-settings' ); ?>
		</p>
		<?php
	}

	public function saucal_test_endpoint_cb( $args ) {
		// get the value of the setting we've registered with register_setting()
		$option = get_option( 'saucal_test_endpoint' );
		// output the field
		?>
		<input type="url"
			id="<?php esc_attr( $args['label_for'] ); ?>"
			class="<?php esc_attr( $args['class'] ); ?>"
			name="<?php esc_attr( $args['label_for'] ); ?>"
			value="
			<?php
			if ( isset( $option ) ) :
				echo $option;
endif;
?>
" />

		<p class="description">
			<?php esc_html( 'Enter your test API endpoint here.', 'saucal-settings' ); ?>
		</p>
		<?php
	}
	public function saucal_checkbox( $args ) {
		$option = get_option( $args['option'] );
		// output the field
		?>
		<input type="checkbox"
			id="<?php esc_attr( $args['label_for'] ); ?>"
			class="<?php esc_attr( $args['class'] ); ?>"
			name="<?php esc_attr( $args['label_for'] ); ?>"
			<?php
			if ( isset( $option ) && 'on' === $option ) :
?>
checked=checked<?php endif; ?>" />

		<p class="description">
			<?php esc_html( $args['description'] ); ?>
		</p>
		<?php
	}


	/**
	 * top level menu
	 */
	public function saucal_options_page() {
		// add top level menu page
		add_submenu_page(
			'options-general.php',
			'saucal Settings',
			'saucal Settings',
			'manage_options',
			'saucal-settings',
			array( $this, 'saucal_options_page_html' )
		);
	}

	/**
	 * top level menu:
	 * callback functions
	 */
	public function saucal_options_page_html() {

		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// add error/update messages
		// check if the user have submitted the settings
		// WordPress will add the "settings-updated" $_GET parameter to the url
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'saucal_messages', 'wporg_message', __( 'Settings Saved', 'saucal-settings' ), 'updated' );
		}

		// show error/update messages
		settings_errors( 'saucal_messages' );

		require_once  plugin_dir_path( __FILE__ ) . 'partials/saucal-plugin-admin-display.php' ;
	}


}
