<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/badbreze
 * @since      1.0.0
 *
 * @package    Kalories
 * @subpackage Kalories/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Kalories
 * @subpackage Kalories/includes
 * @author     Damian Gomez <racksoft@gmail.com>
 */
class Kalories_Deactivator {

	/**
	 * When the user deactivates the plugin
	 *
	 * All data and table are expected to be dropped, no trash please
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        global $wpdb;

        $table_name = $wpdb->prefix . "kalories";

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "DROP TABLE $table_name;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        //Drop Table and data
        dbDelta( $sql );
	}

}
