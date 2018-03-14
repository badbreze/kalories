<form method="get" class="alignleft actions daterangeactions">
    <input type="hidden" name="page" value="kalories_takeout">
    <span><?= __('From') ?>: </span>
    <input type="date" name="from-date" id="picker-from" value="<?= $_GET['from-date']; ?>"/>
    <span> - <?= __('To') ?>: </span>
    <input type="date" name="to-date" id="picker-to" value="<?= $_GET['to-date']; ?>"/>
    <?php submit_button(__('Apply', 'iw-stats'), 'action', 'date-filter', false); ?>
</form>