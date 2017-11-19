<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>

<form action="lib/reports.php" method="GET" target="_BLANK">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-4">
            <label for="type"><?php lang("report type"); ?></label>
            <select name="type" class="form-control" required>
                <option value="shifts"><?php lang("shifts") ?></option>
            </select>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <label for="users"><?php lang("filter"); ?></label>
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
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <label for="type"><?php lang("format"); ?></label>
            <select name="format" class="form-control" required>
                <option value="csv"><?php lang("csv file") ?></option>
                <option value="ods"><?php lang("ods file") ?></option>
                <option value="html"><?php lang("html file") ?></option>
            </select>
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