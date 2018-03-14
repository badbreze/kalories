<?php

/**
 * A report table for Kalories Plugin
 *
 * @link       https://github.com/badbreze
 * @since      1.0.0
 *
 * @package    Kalories
 * @subpackage Kalories/admin
 */

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Here we go, let describe this custom table for user data listing
 *
 * @package    Kalories
 * @subpackage Kalories/admin
 * @author     Damian Gomez <racksoft@gmail.com>
 */
class Kalories_Takeout_Table extends WP_List_Table
{

    /**
     * Kalories_Takeout_Table constructor.
     */
    function __construct()
    {
        parent::__construct(array(
            'singular' => 'wp_list_text_link',
            'plural' => 'wp_list_test_links',
            'ajax' => false
        ));
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

    /**
     * Filter component
     * @param $which
     */
    public function extra_tablenav($which)
    {
        require plugin_dir_path(dirname(__FILE__)) . 'admin/partials/kalories-takeout-date-filter.php';
    }

    /**
     * Define the report columns
     * @return array
     */
    function get_columns()
    {
        return array(
            //'cb' => '<input type="checkbox" />',
            'text' => __('Text'),
            'date' => __('Date'),
            'time' => __('Time'),
            'calories' => __('Calories')
        );
    }

    /**
     * Set the sortable columns
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        return array(
            'text' => array('text', true),
            'date' => array('date', false),
            'time' => array('time', true),
            'calories' => array('calories', true)
        );
    }

    /**
     * Define columns to reppresent
     * @param $item
     * @param $column_name
     * @return mixed
     */
    function column_default( $item, $column_name ) {
        $kalories_limit = get_option('kalories_limit') ?: 0;

        switch( $column_name ) {
            case 'text':
            case 'date':
            case 'time':
                return $item->{$column_name};
            case 'calories':
                $color = "";

                if($kalories_limit != 0 && $item->calories > $kalories_limit)
                    $color = "kal_red";
                elseif($kalories_limit != 0 && $item->calories <= $kalories_limit)
                    $color = "kal_green";

                    return "<span class='{$color}'>" . $item->{$column_name} . "</span>";
                break;
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Checkboxes for bulk selection
     *
     * @param $item
     * @return string
     */
    /*function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="book[]" value="%s" />', $item->{ID}
        );
    }*/

    /**
     * Bulk options, in this case only delete
     * @return array
     */
    /*protected function get_bulk_actions()
    {
        return array('delete' => 'Elimina');
    }*/

    /**
     * @param $id
     * @return string
     */
    public function get_edit_post_link($id) {
        return "admin.php?page=kalories_report&id={$id}";
    }

    /**
     * @param $id
     * @return string
     */
    public function get_delete_post_link($id) {
        return "admin.php?page=kalories_takeout&delete=true&id={$id}";
    }

    /**
     * Generates and displays row action links.
     *
     * @since 4.3.0
     *
     * @param object $post        Post being acted upon.
     * @param string $column_name Current column name.
     * @param string $primary     Primary column name.
     * @return string Row actions output for posts.
     */
    protected function handle_row_actions( $item, $column_name, $primary ) {
        if ( $primary !== $column_name ) {
            return '';
        }

        $title = $item->text;
        $actions = array();

        $actions['edit'] = sprintf(
            '<a href="%s" aria-label="%s">%s</a>',
            $this->get_edit_post_link( $item->id ),
            /* translators: %s: post title */
            esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ),
            __( 'Edit' )
        );

        $actions['delete'] = sprintf(
            '<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
            $this->get_delete_post_link( $item->id ),
            /* translators: %s: post title */
            esc_attr( sprintf( __( 'Delete &#8220;%s&#8221;' ), $title ) ),
            __( 'Delete' )
        );

        return $this->row_actions( $actions );
    }

    /**
     * Data fetching and preparation for user
     */
    function prepare_items()
    {
        global $wpdb, $_wp_column_headers;

        $screen = get_current_screen();
        $user_id = $this->get_current_user_id();

        //Prepare query
        $query = "SELECT * FROM {$wpdb->prefix}kalories WHERE user_id={$user_id}";

        //Date filtering
        if (!empty($_GET["from-date"])) {
            $mysqlDate = date ("Y-m-d", strtotime($_GET["from-date"]));

            $query .= ' AND date >= "' . $mysqlDate . '"';
        }

        if (!empty($_GET["to-date"])) {
            $mysqlDate = date ("Y-m-d", strtotime($_GET["to-date"]));

            $query .= ' AND date <= "' . $mysqlDate . '"';
        }

        //Parameters that are going to be used to order the result
        $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
        $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';

        if (!empty($orderby) & !empty($order)) {
            $query .= ' ORDER BY ' . $orderby . ' ' . $order;
        } else {
            $query .= ' ORDER BY date, time ';
        }

        //Number of elements in your table?
        $totalitems = $wpdb->query($query); //return the total number of affected rows

        //How many to display per page?
        $perpage = 25;

        //Which page is this?
        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';

        //Page Number
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }

        //How many pages do we have in total? $totalpages = ceil($totalitems/$perpage);
        //adjust the query to take pagination into account if(!empty($paged) && !empty($perpage)){ $offset=($paged-1)*$perpage; $query.=' LIMIT '.(int)$offset.','.(int)$perpage; }

        /* -- Register the pagination -- */
        $this->set_pagination_args(
            array(
                "total_items" => $totalitems,
                "total_pages" => $totalpages,
                "per_page" => $perpage
            )
        );

        /* -- Register the Columns -- */
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();

        $_wp_column_headers[$screen->id] = $columns;
        $this->_column_headers = array($columns, $hidden, $sortable);

        /* -- Fetch the items -- */
        $this->items = $wpdb->get_results($query);
    }

}
