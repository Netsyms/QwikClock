/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

var options = {
    url: "action.php",
    ajaxSettings: {
        dataType: "json",
        method: "GET",
        data: {
            action: "autocomplete_user"
        }
    },
    preparePostData: function (data) {
        data.q = $("#people-box").val();
        return data;
    },
    getValue: function (element) {
        return element.username;
    },
    template: {
        type: "custom",
        method: function (value, item) {
            return item.name + " <i class=\"small\">" + item.username + "</i>";
        }
    },
    list: {
        onClickEvent: function () {
            var value = $("#people-box").getSelectedItemData().username;
            addPerson(value);
        }
    },
    requestDelay: 500,
    cssClasses: "form-control form-control-sm"
};

$("#people-box").easyAutocomplete(options);

$("#people-box").keyup(function (event) {
    if (event.keyCode == 13) {
        $("#addpersonbtn").click();
        event.preventDefault();
        return false;
    }
});
$("#people-box").keydown(function (event) {
    if (event.keyCode == 13) {
        event.preventDefault();
        return false;
    }
});

$("#addpersonbtn").click(function () {
    addPerson($("#people-box").val());
});

function addPerson(p) {
    p = p.trim();
    if (p == "") {
        return false;
    }
    if ($("#peoplelist div[data-user=" + p + "]").length) {
        $("#peoplelist .list-group-item[data-user=" + p + "]").animate({
            backgroundColor: "#ff0000",
        }, 500, "linear", function () {
            $("#peoplelist .list-group-item[data-user=" + p + "]").animate({
                backgroundColor: "#ffffff",
            }, 500);
        });
        return false;
    }
    $('#peoplelist').append("<div class=\"list-group-item\" data-user=\"" + p + "\">" + p + "<div class=\"btn btn-danger btn-sm float-right rmperson\"><i class=\"fas fa-trash\"></i></div><input type=\"hidden\" name=\"users[]\" value=\"" + p + "\" /></div>");
    $("#people-box").val("");
}

function removePerson(p) {
    $("#peoplelist div[data-user=" + p + "]").remove();
}

$('#shift-select').on('change', function () {
    document.location.href = "app.php?page=assignshift&shift=" + $(this).val();
});

$('#peoplelist').on("click", ".rmperson", function () {
    removePerson($(this).parent().data("user"));
});