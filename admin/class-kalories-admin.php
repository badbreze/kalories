<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/badbreze
 * @since      1.0.0
 *
 * @package    Kalories
 * @subpackage Kalories/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Kalories
 * @subpackage Kalories/admin
 * @author     Damian Gomez <racksoft@gmail.com>
 */
class Kalories_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Kalories_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Kalories_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/kalories-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Kalories_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Kalories_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/kalories-admin.js', array('jquery'), $this->version, false);

    }

    /**
     * Place new settings
     */
    public function register_settings()
    {
        register_setting('kalories', 'kalories_limit');
    }

    /**
     * Add required admin menu items
     */
    public function register_pages()
    {
        //Register The Configuration Page
        add_options_page('Kalories', 'Kalories', 'activate_plugins', 'kalories', [$this, 'page_configure']);

        //Register Reporting Page
        add_menu_page('Kalories Report', 'Kalories Report', 'publish_posts', 'kalories_report', [$this, 'page_report']);

        //Register User Log Page
        add_submenu_page('kalories_report', 'Kalories Takeout', 'Kalories Takeout', 'publish_posts', 'kalories_takeout', [$this, 'page_takeout']);
    }

    /**
     * This is a configuration page for admin only
     */
    public function page_configure()
    {
        require __DIR__ . "/partials/kalories-configure.php";
    }

    /**
     * This is the report page, we expect to report user data and store on db
     */
    public function page_report()
    {
        //If in edit mode the page is the same but data is from DB
        $report = $this->report_pickup();

        //Verify permission on record
        if ($report && $report['user_id'] != $this->get_current_user_id()) {
            wp_die(__('Sorry, you are not allowed to edit this record.'));
        }

        //Store data if needed
        if (!empty($_POST['kalories'])) {
            $row = $this->store_row($_POST['kalories'], $report);

            return require __DIR__ . "/partials/kalories-report-saved.php";
        }

        return require __DIR__ . "/partials/kalories-report.php";
    }

    /**
     * Fetch for report by id and return as associative array
     * @return bool
     */
    public function report_pickup()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "kalories";

        //If id is passed find the row
        if (isset($_GET['id'])) {
            $report_id = (int)$_GET['id'];

            $row = $wpdb->get_row("SELECT * FROM {$table_name} WHERE id = {$report_id}", ARRAY_A);

            return $row;
        }

        return false;
    }

    /**
     * Store a report, if id is set update the existing
     * @return mixed
     */
    public function store_row($data, $report = false)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "kalories";

        //If the report is set update existing row
        if ($report) {
            return $wpdb->update($table_name, $data, [
                'id' => $report['id']
            ], [
                '%s',
                '%d',
                '%s',
                '%s'
            ]);
        }

        //The user to store row
        $user_id = $this->get_current_user_id();

        $storeData = [
            'text' => $data['text'],
            'calories' => $data['calories'],
            'date' => $data['date'],
            'time' => $data['time'],
            'user_id' => $user_id
        ];

        //Or create new row
        return $wpdb->insert(
            $table_name,
            $storeData,
            [
                '%s',
                '%d',
                '%s',
                '%s',
                '%d'
            ]
        );
    }

    /**
     * The user reported data will be listed here
     */
    public function page_takeout()
    {
        //Drop row if requested
        if (isset($_GET['delete'])) {
            $this->delete_row($_GET['id']);
        }

        require __DIR__ . "/partials/kalories-takeout.php";
    }

    /**
     * Drop a row by id if the user is authorized
     * @param $id
     * @return mixed
     */
    public function delete_row($id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "kalories";

        //If in edit mode the page is the same but data is from DB
        $report = $this->report_pickup();

        //Verify permission on record
        if ($report && $report['user_id'] != $this->get_current_user_id()) {
            wp_die(__('Sorry, you are not allowed to edit this record.'));
        }

        $report_id = (int)$_GET['id'];

        return $wpdb->delete($table_name, ['id' => $report_id]);
    }

    /**
     * We need the user id for filtering
     *
     * @return int
     */
    function get_current_user_id()
    {
        if (!function_exists('wp_get_current_user'))
            return 0;

        $user = wp_get_current_user();

        return (isset($user->ID) ? (int)$user->ID : 0);
    }

}
