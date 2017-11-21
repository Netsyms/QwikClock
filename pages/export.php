<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

if (!account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE")) {
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
                                <select name="type" class="form-control" required>
                                    <option value="shifts"><?php lang("shifts") ?></option>
                                    <option value="punches"><?php lang("punches") ?></option>
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
                        <div class="radio">
                            <label>
                                <input name="users" value="all" checked="" type="radio"> <i class="fa fa-users fa-fw"></i>
                                <?php lang("all users") ?>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input name="users" value="one" type="radio"> <i class="fa fa-user fa-fw"></i>
                                <?php lang("one user") ?>
                                <input type="text" name="user" class="form-control" id="user-box" placeholder="<?php lang("choose user") ?>" />
                            </label>
                        </div>
                        <hr />
                        <label><i class="fa fa-calendar"></i> <?php lang("date range") ?></label><br />
                        <div class="input-group">
                            <input type="text" id="startdate" name="startdate" class="form-control" />
                            <span class="input-group-addon"><i class="fa fa-chevron-right"></i></span>
                            <input type="text" id="enddate" name="enddate" class="form-control" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <?php
        $code = uniqid(rand(10000000, 99999999), true);
        $database->insert('report_access_codes', ['code' => $code, 'expires' => date("Y-m-d H:i:s", strtotime("+5 minutes"))]);
        ?>
        <input type="hidden" name="code" value="<?php echo $code; ?>" />

        <button type="submit" class="btn btn-success" id="genrptbtn"><i class="fa fa-download"></i> <?php lang("generate report"); ?></button>
    </form>
    <?php
}
?>