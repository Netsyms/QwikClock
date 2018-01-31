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
            <div class="col-12 col-md-6">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-12 col-lg-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h4 class="card-title"><label for="type"><i class="fas fa-list"></i> <?php lang("report type"); ?></label></h4>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="shifts"><?php lang("shifts") ?></option>
                                    <option value="punches"><?php lang("punches") ?></option>
                                    <option value="totals"><?php lang("totals") ?></option>
                                    <option value="jobs"><?php lang("jobs") ?></option>
                                    <option value="alljobs"><?php lang("all jobs") ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-12 col-lg-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h4 class="card-title"><label for="format"><i class="fas fa-file"></i> <?php lang("format"); ?></label></h4>
                                <select name="format" class="form-control" required>
                                    <option value="csv"><?php lang("csv file") ?></option>
                                    <option value="ods"><?php lang("ods file") ?></option>
                                    <option value="html"><?php lang("html file") ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success btn-block d-none d-lg-block genrptbtn"><i class="fas fa-download"></i> <?php lang("generate report"); ?></button>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title"><label><i class="fas fa-filter"></i> <?php lang("filter"); ?></label></h4>
                                <div id="user-filter">
                                    <div class="form-check">
                                        <input name="users" value="all" checked="" type="radio" class="form-check-input">
                                        <label class="form-check-label"><i class="fas fa-users fa-fw"></i> <?php lang("all managed users") ?></label>
                                    </div>
                                    <div class="form-check">
                                        <input name="users" value="one" type="radio" class="form-check-input">
                                        <label class="form-check-label"><i class="fas fa-user fa-fw"></i> <?php lang("one user") ?>
                                            <div class="form-group mb-0" id="user-selection">
                                                <input type="text" name="user" class="form-control" id="user-box" placeholder="<?php lang("choose user") ?>" />
                                                <label class="control-label" id="user-not-managed-text" for="user-box"><i class="fas fa-warning"></i> <?php lang("not a managed user") ?></label>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div id="date-filter">
                                    <label><i class="fas fa-calendar"></i> <?php lang("date range") ?></label><br />
                                    <div class="input-group">
                                        <input type="text" id="startdate" name="startdate" data-toggle="datetimepicker" data-target="#startdate" class="form-control" />
                                        <span class="input-group-text"><i class="fas fa-chevron-right"></i></span>
                                        <input type="text" id="enddate" name="enddate" data-toggle="datetimepicker" data-target="#enddate" class="form-control" />
                                    </div>
                                </div>
                                <div id="deleted-filter">
                                    <div class="form-check">
                                        <input name="deleted" value="1" checked="1" type="checkbox" class="form-check-input">
                                        <label class="form-check-label" for="deleted"><i class="fas fa-trash fa-fw"></i> <?php lang("include deleted") ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <?php
                        $code = uniqid(rand(10000000, 99999999), true);
                        $database->insert('report_access_codes', ['code' => $code, 'expires' => date("Y-m-d H:i:s", strtotime("+5 minutes")), 'uid' => $_SESSION['uid']]);
                        ?>
                        <input type="hidden" name="code" value="<?php echo $code; ?>" />

                        <button type="submit" class="btn btn-success btn-block d-lg-none genrptbtn"><i class="fas fa-download"></i> <?php lang("generate report"); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php
}
?>