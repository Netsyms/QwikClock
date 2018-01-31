<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';
require_once __DIR__ . '/../lib/login.php';
require_once __DIR__ . '/../lib/userinfo.php';

redirectifnotloggedin();

$data = [
    "punchid" => "",
    "uid" => "",
    "in" => "",
    "out" => "",
    "notes" => "",
    "username" => ""
];

$editing = false;
$ownpunch = false;
if (isset($VARS['pid']) && $database->has('punches', ['punchid' => $VARS['pid']])) {
    $editing = true;
    $data = $database->get('punches', [
        "punchid",
        "uid",
        "in",
        "out",
        "notes",
        "shiftid"
            ], [
        'punchid' => $VARS['pid']
    ]);
    if ($data["uid"] == $_SESSION['uid']) {
        $ownpunch = true;
    }
}

if ($ownpunch) {
    if (!account_has_permission($_SESSION['username'], "QWIKCLOCK_EDITSELF")) {
        header("Location: app.php?page=punches&msg=no_editself_permission");
        die();
    }
} else {
    if (account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
        // All good
    } else if (account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE")) {
        if ($editing && !isManagerOf($_SESSION['uid'], $data['uid'])) {
            header("Location: app.php?page=punches&msg=you_arent_my_supervisor");
            die();
        }
    } else {
        header("Location: app.php?page=punches&msg=no_permission");
        die();
    }
}
if ($data['uid'] != "") {
    $data['username'] = getUserByID($data['uid'])['username'];
}
?>

<form role="form" action="action.php" method="POST">
    <div class="card border-blue">
        <h4 class="card-header text-blue">
            <?php if ($editing) { ?>
                <i class="fas fa-calendar"></i> <?php lang("edit punch"); ?>
            <?php } else { ?>
                <i class="fas fa-calendar-plus"></i> <?php lang("new punch"); ?>
            <?php } ?>
        </h4>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="form-group">
                        <label for="user"><i class="fas fa-user"></i> <?php lang("user"); ?></label>
                        <input type="text" class="form-control" name="user" id="user" required="required" value="<?php echo $data['username']; ?>" />
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="form-group">
                        <label for="in"><i class="fas fa-play"></i> <?php lang("in"); ?></label>
                        <input type="text" class="form-control" name="in" id="in" required="required" data-toggle="datetimepicker" data-target="#in" value="<?php echo is_empty($data['in']) ? "" : date("D F j Y g:i a", strtotime($data['in'])); ?>" />
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="form-group">
                        <label for="out"><i class="fas fa-stop"></i> <?php lang("out"); ?></label>
                        <input type="text" class="form-control" name="out" id="out" required="required" data-toggle="datetimepicker" data-target="#out" value="<?php echo is_empty($data['out']) ? "" : date("D F j Y g:i a", strtotime($data['out'])); ?>" />
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="notes"><i class="fas fa-sticky-note"></i> <?php lang("notes"); ?></label>
                        <textarea class="form-control" name="notes" maxlength="1000"><?php echo htmlspecialchars($data['notes']); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="punchid" value="<?php echo $data['punchid']; ?>" />
        <input type="hidden" name="action" value="editpunch" />
        <input type="hidden" name="source" value="punches" />

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-success mr-auto"><i class="fas fa-save"></i> <?php lang("save"); ?></button>
            <?php
            if ($editing) {
                ?>
                <a href="action.php?action=deletepunch&source=punches&punchid=<?php echo $data['punchid']; ?>" class="btn btn-danger"><i class="fas fa-times"></i> <?php lang('delete'); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</form>