<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();


require_once __DIR__ . "/../lib/dates.php";
$weekstart = sqldatetime(getstartofweek(WEEK_START));
$punches = $database->select('punches', ['in', 'out'], ['AND' => ['uid' => $_SESSION['uid'], 'in[>]' => $weekstart]]);
$punchtimes = [];
foreach ($punches as $p) {
    $punchtimes[] = [$p['in'], $p['out']];
}
$totalseconds = sumelapsedtimearray($punchtimes);
$totalpunches = count($punches);
?>

<p class="page-header h5"><i class="fa fa-clock-o fa-fw"></i> <?php lang("shifts") ?></p>
<?php
if (account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE")) {
    ?>
    <div class="btn-group" style="margin-bottom: 10px;">
        <a href="app.php?page=editshift" class="btn btn-success"><i class="fa fa-calendar-plus-o"></i> <?php lang("new shift"); ?></a>
        <a href="app.php?page=assignshift" class="btn btn-info"><i class="fa fa-calendar-check-o"></i> <?php lang("assign shift"); ?></a>
    </div>
    <?php
}
?>
<table id="shifttable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="3"></th>
            <th data-priority="3"><i class="fa fa-fw fa-font hidden-xs"></i> <?php lang('name'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-play hidden-xs"></i> <?php lang('start'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-stop hidden-xs"></i> <?php lang('end'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-calendar hidden-xs"></i> <?php lang('days'); ?></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="3"></th>
            <th data-priority="3"><i class="fa fa-fw fa-font hidden-xs"></i> <?php lang('name'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-play hidden-xs"></i> <?php lang('start'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-stop hidden-xs"></i> <?php lang('end'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-calendar hidden-xs"></i> <?php lang('days'); ?></th>
    </tfoot>
</table>

<script>
    /* Give JavaScript access to the lang string
     * it needs to inject the filter checkbox
     */
    var lang_show_all_shifts = "<?php lang("show all shifts") ?>";
</script>