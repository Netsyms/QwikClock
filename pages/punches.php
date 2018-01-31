<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();


require_once __DIR__ . "/../lib/dates.php";
$weekstart = sqldatetime(getstartofweek(WEEK_START));
$weekend = sqldatetime(getstartofweek(WEEK_START) + 604800);
$punches = $database->select('punches', ['in', 'out'], ['AND' => ['uid' => $_SESSION['uid'], 'in[>]' => $weekstart, 'in[<]' => $weekend]]);
$punchtimes = [];
foreach ($punches as $p) {
    $punchtimes[] = [$p['in'], $p['out']];
}
$totalseconds = sumelapsedtimearray($punchtimes);
$totalpunches = count($punches);
?>
<h2 class="mb-4"><i class="fas fa-calendar fa-fw"></i> <?php lang("this week") ?></h2>
<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
        <div class="card-deck">
            <div class="card bg-light-blue text-light">
                <div class="card-body text-center">
                    <h4>
                        <?php
                        lang2("x on the clock", ["time" => seconds2string($totalseconds, false)]);
                        ?>
                    </h4>
                </div>
            </div>
            <div class="card bg-cyan text-light">
                <div class="card-body text-center">
                    <h4>
                        <?php
                        if ($totalpunches != 1) {
                            lang2("x punches", ["count" => $totalpunches]);
                        } else {
                            lang("1 punch");
                        }
                        ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>

<a id="punches">&nbsp;</a>

<h2 class="mb-4 mt-4"><i class="fas fa-clock fa-fw"></i> <?php lang("punch card") ?></h2>
<table id="punchtable" class="table table-bordered table-hover table-sm">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-user hidden-xs"></i> <?php lang('name'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-play hidden-xs"></i> <?php lang('in'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-stop hidden-xs"></i> <?php lang('out'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-sticky-note hidden-xs"></i> <?php lang('notes'); ?></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-user hidden-xs"></i> <?php lang('name'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-play hidden-xs"></i> <?php lang('in'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-stop hidden-xs"></i> <?php lang('out'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-sticky-note hidden-xs"></i> <?php lang('notes'); ?></th>
        </tr>
    </tfoot>
</table>

<script nonce="<?php echo $SECURE_NONCE; ?>">
    /* Give JavaScript access to the lang string
     * it needs to inject the show deleted checkbox
     */
    var lang_show_all_punches = "<?php lang("show all punches") ?>";
</script>