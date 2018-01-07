<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

if (!account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE") && !account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
    ?>
    <div class="alert alert-danger"><?php lang("missing permission") ?></div>
    <?php
} else {
    ?>
    <form action="lib/reports.php" method="GET" target="_BLANK">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><label for="type"><i class="fa fa-list"></i> <?php lang("report type"); ?></label></h3>
                            </div>
                            <div class="panel-body">
                                <select name="type" id="type" class="form-control" required>
                                    <option value="shifts"><?php lang("shifts") ?></option>
                                    <option value="punches"><?php lang("punches") ?></option>
                                    <option value="totals"><?php lang("totals") ?></option>
                                    <option value="alljobs"><?php lang("all jobs") ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><label for="format"><i class="fa fa-file"></i> <?php lang("format"); ?></label></h3>
                            </div>
                            <div class="panel-body">
                                <select name="format" class="form-control" required>
                                    <option value="csv"><?php lang("csv file") ?></option>
                                    <option value="ods"><?php lang("ods file") ?></option>
                                    <option value="html"><?php lang("html file") ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><label><i class="fa fa-filter"></i> <?php lang("filter"); ?></label></h3>
                    </div>
                    <div class="panel-body">
                        <div id="user-filter">
                            <div class="radio">
                                <label>
                                    <input name="users" value="all" checked="" type="radio"> <i class="fa fa-users fa-fw"></i>
                                    <?php lang("all managed users") ?>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input name="users" value="one" type="radio"> <i class="fa fa-user fa-fw"></i>
                                    <?php lang("one user") ?>
                                    <div class="form-group" id="user-selection">
                                        <input type="text" name="user" class="form-control" id="user-box" placeholder="<?php lang("choose user") ?>" />
                                        <label class="control-label" id="user-not-managed-text" for="user-box"><i class="fa fa-warning"></i> <?php lang("not a managed user") ?></label>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div id="date-filter">
                            <label><i class="fa fa-calendar"></i> <?php lang("date range") ?></label><br />
                            <div class="input-group">
                                <input type="text" id="startdate" name="startdate" class="form-control" />
                                <span class="input-group-addon"><i class="fa fa-chevron-right"></i></span>
                                <input type="text" id="enddate" name="enddate" class="form-control" />
                            </div>
                        </div>
                        <div id="deleted-filter">
                            <div class="checkbox">
                                <label>
                                    <input name="deleted" value="1" checked="1" type="checkbox"> <i class="fa fa-trash fa-fw"></i>
                                    <?php lang("include deleted") ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <?php
        $code = uniqid(rand(10000000, 99999999), true);
        $database->insert('report_access_codes', ['code' => $code, 'expires' => date("Y-m-d H:i:s", strtotime("+5 minutes")), 'uid' => $_SESSION['uid']]);
        ?>
        <input type="hidden" name="code" value="<?php echo $code; ?>" />

        <button type="submit" class="btn btn-success" id="genrptbtn"><i class="fa fa-download"></i> <?php lang("generate report"); ?></button>
    </form>
    <?php
}
?>