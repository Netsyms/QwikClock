<?php
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
    <div class="panel panel-blue">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?php if ($editing) { ?>
                    <i class="fa fa-calendar-o"></i> <?php lang("edit punch"); ?>
                <?php } else { ?>
                    <i class="fa fa-calendar-plus-o"></i> <?php lang("new punch"); ?>
                <?php } ?>
            </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="form-group">
                        <label for="user"><i class="fa fa-user"></i> <?php lang("user"); ?></label>
                        <input type="text" class="form-control" name="user" id="user" required="required" value="<?php echo $data['username']; ?>" />
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="form-group">
                        <label for="in"><i class="fa fa-play"></i> <?php lang("in"); ?></label>
                        <input type="text" class="form-control" name="in" id="in" required="required" value="<?php echo is_empty($data['in']) ? "" : date("D F j Y g:i a", strtotime($data['in'])); ?>" />
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="form-group">
                        <label for="out"><i class="fa fa-stop"></i> <?php lang("out"); ?></label>
                        <input type="text" class="form-control" name="out" id="out" required="required" value="<?php echo is_empty($data['out']) ? "" : date("D F j Y g:i a", strtotime($data['out'])); ?>" />
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label for="notes"><i class="fa fa-sticky-note-o"></i> <?php lang("notes"); ?></label>
                        <textarea class="form-control" name="notes" maxlength="1000"><?php echo htmlspecialchars($data['notes']); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="punchid" value="<?php echo $data['punchid']; ?>" />
        <input type="hidden" name="action" value="editpunch" />
        <input type="hidden" name="source" value="punches" />

        <div class="panel-footer">
            <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> <?php lang("save"); ?></button>
            <?php
            if ($editing) {
                ?>
                <a href="action.php?action=deletepunch&source=punches&punchid=<?php echo $data['punchid']; ?>" class="btn btn-danger btn-xs pull-right mgn-top-8px"><i class="fa fa-times"></i> <?php lang('delete'); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</form>