<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="panel panel-blue">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-clock-o"></i> <?php lang("this week"); ?>
                </h3>
            </div>
            <div class="panel-body">
                <h4>
                    <?php
                    require_once __DIR__ . "/../lib/dates.php";
                    $weekstart = sqldatetime(getstartofweek(WEEK_START));
                    $punches = $database->select('punches', ['in', 'out'], ['AND' => ['uid' => $_SESSION['uid'], 'in[>]' => $weekstart]]);
                    $punchtimes = [];
                    foreach ($punches as $p) {
                        $punchtimes[] = [$p['in'], $p['out']];
                    }
                    $totalseconds = sumelapsedtimearray($punchtimes);
                    lang2("x on the clock", ["time" => seconds2string($totalseconds, false)]);
                    ?>
                </h4>
            </div>
        </div>
    </div>
</div>
<table id="punchtable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><i class="fa fa-fw fa-play"></i> <?php lang('in'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-stop"></i> <?php lang('out'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-sticky-note-o"></i> <?php lang('notes'); ?></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><i class="fa fa-fw fa-play"></i> <?php lang('in'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-stop"></i> <?php lang('out'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-sticky-note-o"></i> <?php lang('notes'); ?></th>
    </tfoot>
</table>