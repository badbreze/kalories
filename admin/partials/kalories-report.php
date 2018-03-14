<?php
$title = __('Calories Report');

//If not set by the controller create a empty one
if(empty($report)) {
    $report = [
        'text' => '',
        'calories' => '',
        'date' => '',
        'time' => '',
    ];
}

//Priority to post Data
$report = isset($_POST['kalories']) ? $_POST['kalories'] : $report;

//Report id from get, need to compose form action
$report_id = $_GET['id'];
?>

<div class="wrap">
    <h1><?php echo esc_html($title); ?></h1>

    <form method="post" action="admin.php?page=kalories_report<?= $report_id ? '&id='. $report_id : '' ?>" novalidate="novalidate">

        <table class="form-table">

            <tr>
                <th scope="row"><label for="kalories[text]"><?php _e('Name') ?></label></th>
                <td><input name="kalories[text]" type="text" id="text" value="<?= $report['text']; ?>" class="regular-text"/></td>
            </tr>

            <tr>
                <th scope="row"><label for="kalories[calories]"><?php _e('Calories') ?></label></th>
                <td><input name="kalories[calories]" type="number" id="calories" value="<?= $report['calories']; ?>" class="regular-text"/></td>
            </tr>

            <tr>
                <th scope="row"><label for="kalories[date]"><?php _e('Date') ?></label></th>
                <td><input name="kalories[date]" type="date" id="date" value="<?= $report['date']; ?>" class="regular-text"/></td>
            </tr>

            <tr>
                <th scope="row"><label for="kalories[time]"><?php _e('Time') ?></label></th>
                <td><input name="kalories[time]" type="time" id="time" value="<?= $report['time']; ?>" class="regular-text"/></td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>