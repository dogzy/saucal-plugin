<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.jonnyrudd.co.uk
 * @since      1.0.0
 *
 * @package    Saucal_Plugin
 * @subpackage Saucal_Plugin/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<!-- Create a header in the default WordPress 'wrap' container -->
	<div class="wrap">

			 <div id="icon-themes" class="icon32"></div>
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<?php settings_errors(); ?>
   
	<form action="options.php" method="post">
		<?php
		// output security fields for the registered setting "wporg"
		settings_fields( 'saucal-settings' );
		// output setting sections and their fields
		// (sections are registered for "wporg", each field is registered to a specific section)
		do_settings_sections( 'saucal-settings' );
		// output save settings button
		submit_button( 'Save Settings' );
		?>
	</form>

			 </div><!-- /.wrap -->
