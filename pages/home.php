<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="row">

    <div class="col-xs-12 col-sm-6 col-md-4 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-body" style="text-align: center;">
                <h2 id="server_time"><?php echo date(TIME_FORMAT); ?></h2>
                <h4 id="server_date"><?php echo date(LONG_DATE_FORMAT); ?></h4>
            </div>
            <div id="seconds_bar" style="width: 100%; height: 5px; padding-bottom: 5px;">
                <div style="background-color: #ffc107; height: 5px; width: <?php echo round(date('s') * 1 / 60 * 100, 4); ?>%;"></div>
            </div>
        </div>
    </div>


    <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="panel panel-blue">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-clock-o"></i> <?php lang("punch in out"); ?>
                </h3>
            </div>
            <div class="panel-body">
                <a href="action.php?source=home&action=punchin" class="btn btn-block btn-success btn-lg"><i class="fa fa-play"></i> <?php lang("punch in"); ?></a>
                <br />
                <a href="action.php?source=home&action=punchout" class="btn btn-block btn-danger btn-lg"><i class="fa fa-stop"></i> <?php lang("punch out"); ?></a>
            </div>
            <div class="panel-footer">
                <?php
                $in = $database->has('punches', ['AND' => ['uid' => $_SESSION['uid'], 'out' => null]]) === TRUE;
                ?>
                <i class="fa fa-info-circle"></i> <span id="inmsg"<?php echo ($in ? '' : ' style="display: none;"') ?>><?php lang("you are punched in"); ?></span><span id="outmsg"<?php echo ($in ? ' style="display: none;"' : '') ?>><?php lang("you are not punched in"); ?></span>
                <br />
                <a href="app.php?page=punches" style="color: #01579b;"><i class="fa fa-arrow-right"></i> <?php lang("view punch card"); ?></a>
            </div>
        </div>
    </div>

</div>