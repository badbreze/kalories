<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/badbreze
 * @since      1.0.0
 *
 * @package    Kalories
 * @subpackage Kalories/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Kalories
 * @subpackage Kalories/includes
 * @author     Damian Gomez <racksoft@gmail.com>
 */
class Kalories_Activator {

	/**
	 * When the plugin is activated
	 *
	 * Support table will be created
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        global $wpdb;

        $table_name = $wpdb->prefix . "kalories";

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table_name} (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) NOT NULL,
            `date` date NOT NULL,
            `time` time NOT NULL,
            `text` text NULL,
            `calories` mediumint(9) NOT NULL,
            PRIMARY KEY  (id)
        ) {$charset_collate};";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        //Create the table
        return dbDelta( $sql );
    }

}
