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
        <a href="app.php?page=editjob" class="btn btn-success"><i class="fa fa-plus"></i> <?php lang("add job"); ?></a>
        <?php
    }
    ?>
</div>

<table id="jobtable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-briefcase hidden-xs"></i> <?php lang('name'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-barcode hidden-xs"></i> <?php lang('code'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $jobs = $database->select('jobs', ['jobid (id)', 'jobname (name)', 'jobcode (code)', 'color'], ['deleted' => 0]);
        foreach ($jobs as $j) {
            echo "<tr><td></td><td>" . '<a class="btn btn-primary btn-xs" href="app.php?page=editjob&job=' . $j['id'] . '"><i class="fa fa-pencil-square-o"></i> ' . lang("edit", false) . '</a>' . "</td><td>" . '<span class="label label-' . $j['color'] . '">&nbsp;&nbsp;</span> ' . $j['name'] . "</td><td>" . $j['code'] . "</td></tr>";
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-briefcase hidden-xs"></i> <?php lang('name'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-barcode hidden-xs"></i> <?php lang('code'); ?></th>
        </tr>
    </tfoot>
</table>