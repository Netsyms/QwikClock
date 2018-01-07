<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group mgn-btm-10px">
    <?php
    if (account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
        ?>
        <a href="app.php?page=editjobs" class="btn btn-primary"><i class="fa fa-pencil"></i> <?php lang("edit jobs"); ?></a>
        <?php
    }
    ?>
</div>

<p class="page-header h5"><i class="fa fa-briefcase fa-fw"></i> <?php lang("select a job") ?></p>
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
        $jobs = $database->select('jobs', ['[>]job_groups' => ['jobid']], ['jobs.jobid', 'jobname', 'jobcode', 'color'], ["AND" => ["OR" => ['groupid' => $gids, 'groupid #-1' => -1], 'deleted' => 0]]);
    } else {
        $jobs = $database->select('jobs', ['jobid', 'jobname', 'jobcode', 'color'], ['deleted' => 0]);
    }

    foreach ($jobs as $job) {
        $color = "default";
        if (!is_null($job['color']) && $job['color'] != "") {
            $color = $job['color'];
        }
        ?>
        <a class="job-btn" href="action.php?action=setjob&source=jobs&job=<?php echo $job['jobid']; ?>">
            <span class="btn btn-<?php echo $color; ?>"><?php echo $job['jobname']; ?></span>
        </a>
        <?php
    }
    ?>
    <a class="job-btn" href="action.php?action=setjob&source=jobs&job=-1">
        <span class="btn btn-danger"><i class="fa fa-times"></i> <?php lang("none"); ?></span>
    </a>
</div>

<p class="page-header h5"><i class="fa fa-history fa-fw"></i> <?php lang("job history") ?></p>
<table id="jobtable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-briefcase hidden-xs"></i> <?php lang('job'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-play hidden-xs"></i> <?php lang('start'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-stop hidden-xs"></i> <?php lang('end'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-user hidden-xs"></i> <?php lang('user'); ?></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-briefcase hidden-xs"></i> <?php lang('job'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-play hidden-xs"></i> <?php lang('start'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-stop hidden-xs"></i> <?php lang('end'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-user hidden-xs"></i> <?php lang('user'); ?></th>
        </tr>
    </tfoot>
</table>
<script nonce="<?php echo $SECURE_NONCE; ?>">
    var lang_show_all = "<?php lang("show all"); ?>";
</script>