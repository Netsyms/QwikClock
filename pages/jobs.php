<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group mgn-btm-10px">
    <div class="btn btn-default">
        <?php
        lang("current job");

        $currentjob = $database->get('job_tracking', ['[>]jobs' => ['jobid']], ['jobname (name)', 'color'], ["AND" => ["uid" => $_SESSION['uid'], 'end' => null]]);
        if (!$currentjob) {
            $currentjob = ["color" => "white", "name" => lang("none", false)];
        }
        echo ' <span class="badge ml-1 px-2 py-1 badge-' . $currentjob['color'] . '">&nbsp;</span> ' . $currentjob['name'];
        ?>
    </div>
    <?php
    if (account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
        ?>
        <a href="app.php?page=editjobs" class="btn btn-primary"><i class="fas fa-edit"></i> <?php lang("edit jobs"); ?></a>
        <?php
    }
    ?>
</div>

<h2 class="my-4"><i class="fas fa-briefcase fa-fw"></i> <?php lang("select a job") ?></h2>
<div class="container" id="job-btn-bin">
    <?php
    $jobs = [];
    if ($database->count("job_groups") > 0) {
        require_once __DIR__ . "/../lib/userinfo.php";
        $groups = getGroupsByUID($_SESSION['uid']);
        $gids = [];
        foreach ($groups as $g) {
            $gids[] = $g['id'];
        }
        $jobs = $database->select('job_groups', ['[>]jobs' => ['jobid']], ['jobs.jobid', 'jobname', 'jobcode', 'color'], ["AND" => ["OR" => ['groupid' => $gids, 'groupid #-1' => -1], 'deleted' => 0]]);
    } else {
        $jobs = $database->select('jobs', ['jobid', 'jobname', 'jobcode', 'color'], ['deleted' => 0]);
    }

    $jobids = [];
    foreach ($jobs as $job) {
        if (in_array($job['jobid'], $jobids)) {
            continue;
        }
        $jobids[] = $job['jobid'];
        $color = "default";
        if (!is_null($job['color']) && $job['color'] != "") {
            $color = $job['color'];
        }
        ?>
        <a class="job-btn" href="action.php?action=setjob&source=jobs&job=<?php echo $job['jobid']; ?>">
            <span class="btn m-1 btn-<?php echo $color; ?>"><?php echo $job['jobname']; ?></span>
        </a>
        <?php
    }
    ?>
    <a class="job-btn" href="action.php?action=setjob&source=jobs&job=-1">
        <span class="btn btn-danger"><i class="fas fa-times"></i> <?php lang("none"); ?></span>
    </a>
</div>

<h2 class="my-4"><i class="fas fa-history fa-fw"></i> <?php lang("job history") ?></h2>
<table id="jobtable" class="table table-bordered table-hover table-sm">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-briefcase d-none d-md-inline"></i> <?php lang('job'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-play d-none d-md-inline"></i> <?php lang('start'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-stop d-none d-md-inline"></i> <?php lang('end'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-user d-none d-md-inline"></i> <?php lang('user'); ?></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-briefcase d-none d-md-inline"></i> <?php lang('job'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-play d-none d-md-inline"></i> <?php lang('start'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-stop d-none d-md-inline"></i> <?php lang('end'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-user d-none d-md-inline"></i> <?php lang('user'); ?></th>
        </tr>
    </tfoot>
</table>
<script nonce="<?php echo $SECURE_NONCE; ?>">
    var lang_show_all = "<?php lang("show all"); ?>";
</script>