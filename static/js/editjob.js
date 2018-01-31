/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

function addGroup(id, name) {
    id = id.trim();
    if (id == "") {
        return false;
    }
    if ($("#groupslist div[data-groupid=" + id + "]").length) {
        $("#groupslist .list-group-item[data-groupid=" + id + "]").animate({
            backgroundColor: "#ff0000",
        }, 500, "linear", function () {
            $("#groupslist .list-group-item[data-groupid=" + id + "]").animate({
                backgroundColor: "#ffffff",
            }, 500);
        });
        return false;
    }
    $('#groupslist').append("<div class=\"list-group-item\" data-groupid=\"" + id + "\">" + name + "<div class=\"btn btn-danger btn-sm float-right rm\"><i class=\"fas fa-trash\"></i></div><input type=\"hidden\" name=\"groups[]\" value=\"" + id + "\" /></div>");
}

function removeGroup(gid) {
    $("#groupslist div[data-groupid=" + gid + "]").remove();
}

$("#addgroupbtn").click(function () {
    addGroup($("#groups-box").val(), $("#groups-box option:selected").text());
});

$('#groupslist').on("click", ".rm", function () {
    removeGroup($(this).parent().data("groupid"));
});

$('#groups-box').change(function () {
    addGroup($("#groups-box").val(), $("#groups-box option:selected").text());
});