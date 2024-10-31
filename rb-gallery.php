<?php

/**
 *
 * Plugin Name:       RB Image Gallery
 * Plugin URI:        http://devteamseven.com
 * Description:       An album/collection driven image gallery.
 * Version:           2.0.1
 * Author:            DrKnown
 * Author URI:        http://devteamseven.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rb_gallery
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_rb_gallery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/rb-gallery-activator.php';
	Rb_gallery_activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_rb_gallery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/rb-gallery-deactivator.php';
    Rb_gallery_deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_rb_gallery' );
register_deactivation_hook( __FILE__, 'deactivate_rb_gallery' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/rb-gallery-bootstrap.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function run_rbgallery_name() {

	$rb_gallery_plugin = new Rb_gallery_bootstrap();
    $rb_gallery_plugin->run();

}
run_rbgallery_name();	
