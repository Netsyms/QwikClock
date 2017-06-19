<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<table id="punchtable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><i class="fa fa-fw fa-play"></i> <?php lang('in'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-stop"></i> <?php lang('out'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-sticky-note-o"></i> <?php lang('notes'); ?></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><i class="fa fa-fw fa-play"></i> <?php lang('in'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-stop"></i> <?php lang('out'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-sticky-note-o"></i> <?php lang('notes'); ?></th>
    </tfoot>
</table>