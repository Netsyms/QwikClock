<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group mb-4">
    <?php
    if (account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
        ?>
        <a href="app.php?page=editjob" class="btn btn-success"><i class="fa fa-plus"></i> <?php lang("add job"); ?></a>
        <?php
    }
    ?>
</div>

<table id="jobtable" class="table table-bordered table-hover table-sm">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-briefcase d-none d-md-inline"></i> <?php lang('name'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-barcode d-none d-md-inline"></i> <?php lang('code'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $jobs = $database->select('jobs', ['jobid (id)', 'jobname (name)', 'jobcode (code)', 'color'], ['deleted' => 0]);
        foreach ($jobs as $j) {
            echo "<tr><td></td><td>" . '<a class="btn btn-primary btn-sm" href="app.php?page=editjob&job=' . $j['id'] . '"><i class="fas fa-edit"></i> ' . lang("edit", false) . '</a>' . "</td><td>" . '<span class="badge mr-1 px-2 py-1 badge-' . $j['color'] . '">&nbsp;</span> ' . $j['name'] . "</td><td>" . $j['code'] . "</td></tr>";
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-briefcase d-none d-md-inline"></i> <?php lang('name'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-barcode d-none d-md-inline"></i> <?php lang('code'); ?></th>
        </tr>
    </tfoot>
</table>