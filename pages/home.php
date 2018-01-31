<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="row justify-content-center">

    <div class="col-12 col-md-6 col-lg-5 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h1 id="server_time"><?php echo date(TIME_FORMAT); ?></h1>
                <h3 id="server_date"><?php echo date(LONG_DATE_FORMAT); ?></h3>
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


    <div class="col-12 col-md-6 col-lg-5">
        <div class="card bg-blue text-light">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="fas fa-clock"></i> <?php lang("punch in out"); ?>
                </h4>
                <?php
                $in = $database->has('punches', ['AND' => ['uid' => $_SESSION['uid'], 'out' => null]]) === TRUE;
                ?>
                <h5 class="card-subtitle mb-2 ml-4">

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
                </h5>
                <a href="action.php?source=home&action=punchin" class="btn btn-block btn-success btn-lg"><i class="fa fa-play"></i> <?php lang("punch in"); ?></a>
                <br />
                <a href="action.php?source=home&action=punchout" class="btn btn-block btn-danger btn-lg"><i class="fa fa-stop"></i> <?php lang("punch out"); ?></a>
            </div>
            <div class="card-footer">
                <a href="app.php?page=punches#punches" class="text-light"><i class="fas fa-arrow-right"></i> <?php lang("view punch card"); ?></a>
            </div>
        </div>
    </div>

</div>