<?php
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/class-kalories-takeout-table.php';

//Prepare Table of elements
$list_table = new Kalories_Takeout_Table();

//Fetch Items
$list_table->prepare_items();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?= __('Calories Takeout') ?></h1>

    <a href="admin.php?page=kalories_report" class="page-title-action"><?= __('Add Calories Report') ?></a>

    <?php
    //Print the data table
    echo $list_table->display();
    ?>
</div>