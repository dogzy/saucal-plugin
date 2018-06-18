<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.jonnyrudd.co.uk
 * @since             1.0.0
 * @package           Saucal_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Sau/Cal Plugin
 * Plugin URI:        https://www.jonnyrudd.co.uk
 * Description:       This plugin provides a basic wp_remote get feature within my account in WooCommerce. And registers a new feed widget.
 * Version:           1.0.0
 * Author:            Jonny Rudd
 * Author URI:        https://www.jonnyrudd.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       saucal-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-saucal-plugin-activator.php
 */
function activate_saucal_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-saucal-plugin-activator.php';
	Saucal_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-saucal-plugin-deactivator.php
 */
function deactivate_saucal_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-saucal-plugin-deactivator.php';
	Saucal_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_saucal_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_saucal_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-saucal-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_saucal_plugin() {

	$plugin = new Saucal_Plugin();
	$plugin->run();

}
run_saucal_plugin();
