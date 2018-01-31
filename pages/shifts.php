<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>

<div class="btn-toolbar mb-4">
    <div class="btn-group mr-2 mb-2">
        <?php
        if (account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
            ?>
            <a href="app.php?page=editshift" class="btn btn-success"><i class="fas fa-calendar-plus"></i> <?php lang("new shift"); ?></a>
            <?php
        }
        if (account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE") || account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
            ?>
            <a href="app.php?page=assignshift" class="btn btn-info"><i class="fas fa-calendar-check"></i> <?php lang("assign shift"); ?></a>
            <?php
        }
        ?>
    </div>
    <div class="btn-group mr-1 mb-2">
        <span class="btn btn-blue-grey" id="show_btn" data-showall=""><i class="fas fa-filter"></i> <span><?php lang("show all shifts") ?></span></span>
    </div>
    <!--<div class="input-group">
        <span class="input-group-text text-blue-grey" id="showing-all"><i class="fa fa-info-circle"></i> <?php lang("showing all shifts"); ?></span>
    </div>-->
</div>

<table id="shifttable" class="table table-bordered table-hover table-sm">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="3"></th>
            <th data-priority="3"><i class="fas fa-fw fa-font d-none d-md-inline"></i> <?php lang('name'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-play d-none d-md-inline"></i> <?php lang('start'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-stop d-none d-md-inline"></i> <?php lang('end'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-calendar d-none d-md-inline"></i> <?php lang('days'); ?></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="3"></th>
            <th data-priority="3"><i class="fas fa-fw fa-font d-none d-md-inline"></i> <?php lang('name'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-play d-none d-md-inline"></i> <?php lang('start'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-stop d-none d-md-inline"></i> <?php lang('end'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-calendar d-none d-md-inline"></i> <?php lang('days'); ?></th>
        </tr>
    </tfoot>
</table>

<script nonce="<?php echo $SECURE_NONCE; ?>">
    /* Give JavaScript access to the lang string
     * it needs to inject the filter checkbox
     */
    var lang_show_all_shifts = "<?php lang("show all shifts") ?>";
    var lang_show_my_shifts = "<?php lang("show only my shifts") ?>";
</script>