<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

$shifts = $database->select('shifts', [
    "shiftid",
    "shiftname",
    "start",
    "end"]
);
$assigned = [];
require_once __DIR__ . "/../lib/userinfo.php";
$shift = false;
if ($VARS['shift'] && $database->has('shifts', ['shiftid' => $VARS['shift']])) {
    $shift = $VARS['shift'];
    $uids = $database->select('assigned_shifts', 'uid', ['shiftid' => $shift]);
    foreach ($uids as $uid) {
        $assigned[] = getUserByID($uid)['username'];
    }
}
?>

<form role="form" action="action.php" method="POST">
    <div class="card border-blue">
            <h3 class="card-header text-blue">
                <i class="fas fa-calendar"></i> <?php lang("assign shift"); ?>
            </h3>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="shift"><i class="fas fa-font"></i> <?php lang("shift"); ?></label><br />
                        <select name="shift" required="required" class="form-control" id="shift-select">
                            <option value="" selected><?php lang("choose a shift"); ?></option>
                            <?php
                            foreach ($shifts as $s) {
                                $str = $s['shiftname'] . " (" . date(TIME_FORMAT, strtotime($s['start'])) . " - " . date(TIME_FORMAT, strtotime($s['end'])) . ")";
                                $val = $s['shiftid'];
                                ?>
                                <option value="<?php echo $val; ?>"<?php if ($val === $shift) { ?> selected<?php } ?>><?php echo $str; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php
                if ($shift !== false) {
                    ?>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="people-box"><i class="fa fa-user"></i> <?php lang("people"); ?></label><br />
                            <div class="input-group">
                                <input type="text" id="people-box" class="form-control" placeholder="<?php lang("type to add a person") ?>" />
                                <div class="input-group-append">
                                    <button class="btn btn-default" type="button" id="addpersonbtn"><i class="fa fa-plus"></i> <?php lang("add") ?></button>
                                </div>
                            </div>
                        </div>
                        <div class="list-group" id="peoplelist">
                            <?php
                            foreach ($assigned as $user) {
                                ?>
                                <div class="list-group-item" data-user="<?php echo $user; ?>">
                                    <?php echo $user; ?> <div class="btn btn-danger btn-sm float-right rmperson"><i class="fas fa-trash"></i></div><input type="hidden" name="users[]" value="<?php echo $user; ?>" />
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>

        <input type="hidden" name="action" value="assignshift" />
        <input type="hidden" name="source" value="shifts" />

        <div class="card-footer">
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> <?php lang("save"); ?></button>
        </div>
    </div>
</form>