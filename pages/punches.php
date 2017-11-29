<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();


require_once __DIR__ . "/../lib/dates.php";
$weekstart = sqldatetime(getstartofweek(WEEK_START));
$weekend = sqldatetime(getstartofweek(WEEK_START) + 604800);
$punches = $database->select('punches', ['in', 'out'], ['AND' => ['uid' => $_SESSION['uid'], 'in[>]' => $weekstart, 'in[<]' => $weekend]]);
$punchtimes = [];
foreach ($punches as $p) {
    $punchtimes[] = [$p['in'], $p['out']];
}
$totalseconds = sumelapsedtimearray($punchtimes);
$totalpunches = count($punches);
?>
<p class="page-header h5"><i class="fa fa-calendar fa-fw"></i> <?php lang("this week") ?></p>
<div class="row">

    <div class="col-xs-12 col-sm-6 col-md-4 col-md-offset-2">
        <div class="panel panel-blue">
            <div class="panel-body">
                <h4>
                    <?php
                    lang2("x on the clock", ["time" => seconds2string($totalseconds, false)]);
                    ?>
                </h4>
            </div>
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="panel panel-blue">
            <div class="panel-body">
                <h4>
                    <?php
                    if ($totalpunches != 1) {
                        lang2("x punches", ["count" => $totalpunches]);
                    } else {
                        lang("1 punch");
                    }
                    ?>
                </h4>
            </div>
        </div>
    </div>
</div>

<a id="punches">&nbsp;</a>

<p class="page-header h5"><i class="fa fa-clock-o fa-fw"></i> <?php lang("punch card") ?></p>
<table id="punchtable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-user hidden-xs"></i> <?php lang('name'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-play hidden-xs"></i> <?php lang('in'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-stop hidden-xs"></i> <?php lang('out'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-sticky-note-o hidden-xs"></i> <?php lang('notes'); ?></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-user hidden-xs"></i> <?php lang('name'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-play hidden-xs"></i> <?php lang('in'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-stop hidden-xs"></i> <?php lang('out'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-sticky-note-o hidden-xs"></i> <?php lang('notes'); ?></th>
    </tfoot>
</table>

<script nonce="<?php echo $SECURE_NONCE; ?>">
    /* Give JavaScript access to the lang string
     * it needs to inject the show deleted checkbox
     */
    var lang_show_all_punches = "<?php lang("show all punches") ?>";
</script>