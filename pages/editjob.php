<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

$data = [
    "jobid" => "",
    "jobname" => "",
    "jobcode" => "",
    "color" => ""
];

$editing = false;
if (isset($VARS['job']) && $database->has('jobs', ['jobid' => $VARS['job']])) {
    $editing = true;

    $data = $database->get('jobs', [
        "jobid",
        "jobname",
        "jobcode",
        "color",
            ], [
        'jobid' => $VARS['job']
    ]);
}
?>

<form role="form" action="action.php" method="POST">
    <div class="panel panel-blue">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?php if ($editing) { ?>
                    <i class="fa fa-pencil"></i> <?php lang("edit job"); ?>
                <?php } else { ?>
                    <i class="fa fa-plus"></i> <?php lang("new job"); ?>
                <?php } ?>
            </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="jobname"><i class="fa fa-briefcase"></i> <?php lang("name"); ?></label>
                        <input type="text" class="form-control" name="jobname" id="jobname" required="required" value="<?php echo $data['jobname']; ?>" />
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="jobcode"><i class="fa fa-barcode"></i> <?php lang("code"); ?></label>
                        <input type="text" class="form-control" name="jobcode" id="jobcode" value="<?php echo $data['jobcode']; ?>" />
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="color"><i class="fa fa-paint-brush"></i> <?php lang("color"); ?></label>
                        <?php
                        $color = is_null($data['color']) ? "" : $data['color'];
                        ?>
                        <select name="color" class="form-control">
                            <?php
                            $colors = ['', 'Red', 'Pink', 'Purple', 'Deep Purple', 'Indigo', 'Blue', 'Light Blue', 'Cyan', 'Teal', 'Green', 'Light Green', 'Lime', 'Yellow', 'Amber', 'Orange', 'Deep Orange', 'Brown', 'Grey', 'Blue Grey'];

                            function colorToVal($color) {
                                return str_replace(" ", "-", strtolower($color));
                            }

                            foreach ($colors as $c) {
                                $cv = colorToVal($c);
                                if ($c == "") {
                                    $c = lang("none", false);
                                }
                                if ($data['color'] == $cv) {
                                    echo "<option value=\"$cv\" selected>$c</option>";
                                } else {
                                    echo "<option value=\"$cv\">$c</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="jobid" value="<?php echo $data['jobid']; ?>" />
        <input type="hidden" name="action" value="editjob" />
        <input type="hidden" name="source" value="editjobs" />

        <div class="panel-footer">
            <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> <?php lang("save"); ?></button>
            <?php
            if ($editing && account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
                ?>
                <a href="action.php?action=deletejob&source=editjobs&jobid=<?php echo $data['jobid']; ?>" class="btn btn-danger btn-xs pull-right mgn-top-8px"><i class="fa fa-times"></i> <?php lang('delete'); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</form>