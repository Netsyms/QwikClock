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
<table id="shifttable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th data-priority="0"></th>
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