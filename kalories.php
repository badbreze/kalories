<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/badbreze
 * @since             1.0.0
 * @package           Kalories
 *
 * @wordpress-plugin
 * Plugin Name:       Kalories
 * Plugin URI:        https://github.com/badbreze/kalories
 * Description:       This is a simple plugin to manage your health
 * Version:           1.0.0
 * Author:            Damian Gomez
 * Author URI:        https://github.com/badbreze
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       kalories
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
 * This action is documented in includes/class-kalories-activator.php
 */
function activate_kalories() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kalories-activator.php';
	Kalories_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-kalories-deactivator.php
 */
function deactivate_kalories() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kalories-deactivator.php';
	Kalories_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_kalories' );
register_deactivation_hook( __FILE__, 'deactivate_kalories' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-kalories.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_kalories() {

	$plugin = new Kalories();
	$plugin->run();

}
run_kalories();
