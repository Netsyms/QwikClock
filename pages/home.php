<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="row">

    <div class="col-xs-12 col-sm-6 col-md-4 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-body text-center">
                <h2 id="server_time"><?php echo date(TIME_FORMAT); ?></h2>
                <h4 id="server_date"><?php echo date(LONG_DATE_FORMAT); ?></h4>
            </div>
            <div id="seconds_bar">
                <style nonce="<?php echo $SECURE_NONCE; ?>">
                    #seconds_bar_line {
                        width: <?php echo round(date('s') * 1 / 60 * 100, 4); ?>%;
                    }
                </style>
                <div id="seconds_bar_line"></div>
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
                <i class="fa fa-info-circle"></i> 
                <span id="inmsg"><?php lang("you are punched in"); ?></span>
                <span id="outmsg"><?php lang("you are not punched in"); ?></span>
                <style nonce="<?php echo $SECURE_NONCE; ?>">
                    <?php if ($in) { ?>
                    #outmsg {
                        display: none;
                    }
                    <?php } else { ?>
                    #inmsg {
                        display: none;
                    }
                    <?php } ?>
                </style>
                <br />
                <a class="dark-text" href="app.php?page=punches#punches" ><i class="fa fa-arrow-right"></i> <?php lang("view punch card"); ?></a>
            </div>
        </div>
    </div>

</div>