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
    <div class="card border-blue">
        <h3 class="card-header text-blue">
            <?php if ($editing) { ?>
                <i class="fas fa-calendar"></i> <?php lang("edit shift"); ?>
            <?php } else { ?>
                <i class="fas fa-calendar-plus"></i> <?php lang("new shift"); ?>
            <?php } ?>
        </h3>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="shiftname"><i class="fas fa-font"></i> <?php lang("name"); ?></label>
                        <input type="text" class="form-control" name="shiftname" id="shiftname" required="required" value="<?php echo $data['shiftname']; ?>" />
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="start"><i class="fas fa-play"></i> <?php lang("start"); ?></label>
                        <input type="text" class="form-control" name="start" id="start" required="required" data-toggle="datetimepicker" data-target="#start" value="<?php echo $data['start']; ?>" />
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="end"><i class="fas fa-stop"></i> <?php lang("end"); ?></label>
                        <input type="text" class="form-control" name="end" id="end" required="required" data-toggle="datetimepicker" data-target="#end" value="<?php echo $data['end']; ?>" />
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="days"><i class="fas fa-calendar"></i> <?php lang("days"); ?></label>
                        <div id="days-list-container">
                            <div id="days-list">
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" name="days[]" value="Su" <?php if (strpos($data['days'], "Su") !== FALSE) echo "checked"; ?>>
                                    <label class="form-check-label"><?php lang('sunday'); ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" name="days[]" value="Mo" <?php if (strpos($data['days'], "Mo") !== FALSE) echo "checked"; ?>>
                                    <label class="form-check-label"><?php lang('monday'); ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" name="days[]" value="Tu" <?php if (strpos($data['days'], "Tu") !== FALSE) echo "checked"; ?>>
                                    <label class="form-check-label"><?php lang('tuesday'); ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" name="days[]" value="We" <?php if (strpos($data['days'], "We") !== FALSE) echo "checked"; ?>>
                                    <label class="form-check-label"><?php lang('wednesday'); ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" name="days[]" value="Th" <?php if (strpos($data['days'], "Th") !== FALSE) echo "checked"; ?>>
                                    <label class="form-check-label"><?php lang('thursday'); ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" name="days[]" value="Fr" <?php if (strpos($data['days'], "Fr") !== FALSE) echo "checked"; ?>>
                                    <label class="form-check-label"><?php lang('friday'); ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" name="days[]" value="Sa" <?php if (strpos($data['days'], "Sa") !== FALSE) echo "checked"; ?>>
                                    <label class="form-check-label"><?php lang('saturday'); ?></label>
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

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-success mr-auto"><i class="fas fa-save"></i> <?php lang("save"); ?></button>
            <?php
            if ($editing && account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
                ?>
                <a href="action.php?action=deleteshift&source=shifts&shiftid=<?php echo $data['shiftid']; ?>" class="btn btn-danger"><i class="fas fa-times"></i> <?php lang('delete'); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</form>