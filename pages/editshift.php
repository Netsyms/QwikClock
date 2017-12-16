<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

$data = [
    "shiftid" => "",
    "shiftname" => "",
    "start" => "",
    "end" => "",
    "days" => ""
];

$editing = false;
if (isset($VARS['id']) && $database->has('shifts', ['shiftid' => $VARS['id']])) {
    $editing = true;

    $data = $database->get('shifts', [
        "shiftid",
        "shiftname",
        "start",
        "end",
        "days"
            ], [
        'shiftid' => $VARS['id']
    ]);
}
?>

<form role="form" action="action.php" method="POST">
    <div class="panel panel-blue">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?php if ($editing) { ?>
                    <i class="fa fa-calendar-o"></i> <?php lang("edit shift"); ?>
                <?php } else { ?>
                    <i class="fa fa-calendar-plus-o"></i> <?php lang("new shift"); ?>
                <?php } ?>
            </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="shiftname"><i class="fa fa-font"></i> <?php lang("name"); ?></label>
                        <input type="text" class="form-control" name="shiftname" id="shiftname" required="required" value="<?php echo $data['shiftname']; ?>" />
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="start"><i class="fa fa-play"></i> <?php lang("start"); ?></label>
                        <input type="text" class="form-control" name="start" id="start" required="required" value="<?php echo $data['start']; ?>" />
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="end"><i class="fa fa-stop"></i> <?php lang("end"); ?></label>
                        <input type="text" class="form-control" name="end" id="end" required="required" value="<?php echo $data['end']; ?>" />
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="days"><i class="fa fa-calendar"></i> <?php lang("days"); ?></label>
                        <div id="days-list-container">
                            <div id="days-list">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="days[]" value="Su" <?php if (strpos($data['days'], "Su") !== FALSE) echo "checked"; ?>> <?php lang('sunday'); ?>
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="days[]" value="Mo" <?php if (strpos($data['days'], "Mo") !== FALSE) echo "checked"; ?>> <?php lang('monday'); ?>
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="days[]" value="Tu" <?php if (strpos($data['days'], "Tu") !== FALSE) echo "checked"; ?>> <?php lang('tuesday'); ?>
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="days[]" value="We" <?php if (strpos($data['days'], "We") !== FALSE) echo "checked"; ?>> <?php lang('wednesday'); ?>
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="days[]" value="Th" <?php if (strpos($data['days'], "Th") !== FALSE) echo "checked"; ?>> <?php lang('thursday'); ?>
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="days[]" value="Fr" <?php if (strpos($data['days'], "Fr") !== FALSE) echo "checked"; ?>> <?php lang('friday'); ?>
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="days[]" value="Sa" <?php if (strpos($data['days'], "Sa") !== FALSE) echo "checked"; ?>> <?php lang('saturday'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <input type="hidden" name="shiftid" value="<?php echo $data['shiftid']; ?>" />
        <input type="hidden" name="action" value="editshift" />
        <input type="hidden" name="source" value="shifts" />

        <div class="panel-footer">
            <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> <?php lang("save"); ?></button>
            <?php
            if ($editing && account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
                ?>
                <a href="action.php?action=deleteshift&source=shifts&shiftid=<?php echo $data['shiftid']; ?>" class="btn btn-danger btn-xs pull-right mgn-top-8px"><i class="fa fa-times"></i> <?php lang('delete'); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</form>