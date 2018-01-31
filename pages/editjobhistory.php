<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

$data = [
    "id" => "",
    "start" => "",
    "end" => "",
    "jobid" => "",
];

$editing = false;
if (isset($VARS['job']) && $database->has('job_tracking', ['id' => $VARS['job']])) {
    $editing = true;

    $data = $database->get('job_tracking', [
        "id",
        "start",
        "end",
        "jobid"
            ], [
        'id' => $VARS['job']
    ]);
}
?>

<form role="form" action="action.php" method="POST">
    <div class="card border-blue">
        <h3 class="card-header text-blue">
            <?php if ($editing) { ?>
                <i class="fas fa-edit"></i> <?php lang("edit job"); ?>
            <?php } else { ?>
                <i class="fas fa-plus"></i> <?php lang("new job"); ?>
            <?php } ?>
        </h3>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                    <div class="form-group">
                        <label for="job"><i class="fas fa-briefcase"></i> <?php lang("job"); ?></label>
                        <select class="form-control" name="job" required>
                            <option></option>
                            <?php
                            $jobs = [];
                            if ($database->count("job_groups") > 0) {
                                require_once __DIR__ . "/../lib/userinfo.php";
                                $groups = getGroupsByUID($_SESSION['uid']);
                                $gids = [];
                                foreach ($groups as $g) {
                                    $gids[] = $g['id'];
                                }
                                $jobs = $database->select('jobs', ['[>]job_groups' => ['jobid']], ['jobs.jobid', 'jobname'], ["AND" => ["OR" => ['groupid' => $gids, 'groupid #-1' => -1], 'deleted' => 0]]);
                            } else {
                                $jobs = $database->select('jobs', ['jobid', 'jobname'], ['deleted' => 0]);
                            }

                            foreach ($jobs as $job) {
                                ?>
                                <option value="<?php echo $job['jobid']; ?>"<?php echo ($job['jobid'] == $data['jobid'] ? " selected" : ""); ?>><?php echo $job['jobname']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                    <div class="form-group">
                        <label for="start"><i class="fas fa-play"></i> <?php lang("start"); ?></label>
                        <input type="text" class="form-control" name="start" id="start" data-toggle="datetimepicker" data-target="#start" value="<?php echo is_empty($data['start']) ? "" : date("D F j Y g:i a", strtotime($data['start'])); ?>" required />
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                    <div class="form-group">
                        <label for="end"><i class="fas fa-stop"></i> <?php lang("end"); ?></label>
                        <input type="text" class="form-control" name="end" id="end" data-toggle="datetimepicker" data-target="#end" value="<?php echo is_empty($data['end']) ? "" : date("D F j Y g:i a", strtotime($data['end'])); ?>" />
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="jobid" value="<?php echo $VARS['job']; ?>" />
        <input type="hidden" name="action" value="editjobhistory" />
        <input type="hidden" name="source" value="jobs" />

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-success mr-auto"><i class="fas fa-save"></i> <?php lang("save"); ?></button>
            <?php
            if ($editing) {
                ?>
                <a href="action.php?action=deletejobhistory&source=jobs&jobid=<?php echo $VARS['job']; ?>" class="btn btn-danger"><i class="fas fa-times"></i> <?php lang('delete'); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</form>