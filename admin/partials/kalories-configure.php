<?php
if ( ! current_user_can( 'manage_options' ) )
    wp_die( __( 'Sorry, you are not allowed to manage options for this site.' ) );

$title = __('Configure Kalories');
?>

<div class="wrap">
    <h1><?php echo esc_html($title); ?></h1>

    <form method="post" action="options.php" novalidate="novalidate">
        <?php settings_fields('kalories'); ?>

        <table class="form-table">

            <tr>
                <th scope="row"><label for="kalories_limit"><?php _e('Calories Limit') ?></label></th>
                <td><input name="kalories_limit" type="text" id="kalories_limit" value="<?php form_option('kalories_limit'); ?>" class="regular-text"/></td>
            </tr>
        </table>

        <?php do_settings_sections( 'kalories' ); ?>
        <?php submit_button(); ?>
    </form>
</div>