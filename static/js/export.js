/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

$(".genrptbtn").click(function () {
    setTimeout(function () {
        window.location.reload();
    }, 1000)
});

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
        data.q = $("#user-box").val();
        return data;
    },
    getValue: function (element) {
        if (element.managed == 0) {
            $('#user-selection').addClass('has-error');
            $('#user-not-managed-text').css('visibility', '');
        } else {
            $('#user-selection').removeClass('has-error');
            $('#user-not-managed-text').css('visibility', 'hidden');
        }
        return element.username;
    },
    template: {
        type: "custom",
        method: function (value, item) {
            if (item.managed == 0) {
                return "<span class=\"red\">" + item.name + " <i class=\"small\">" + item.username + "</i></span>";
            } else {
                return item.name + " <i class=\"small\">" + item.username + "</i>";
            }
        }
    }
};

$("#user-box").easyAutocomplete(options);

$('#user-box').on("keypress", function () {
    $('#user-not-managed-text').css('visibility', 'hidden');
    $('#user-selection').removeClass('has-error');
});

$(function () {
    $('#startdate').datetimepicker({
        format: "MMM D YYYY",
        useCurrent: false,
        icons: {
            time: "fas fa-clock",
            date: "fas fa-calendar",
            up: "fas fa-arrow-up",
            down: "fas fa-arrow-down"
        }
    });
    $('#enddate').datetimepicker({
        format: "MMM D YYYY"/*"YYYY-M-DTH:m"*/,
        useCurrent: true,
        icons: {
            time: "fas fa-clock",
            date: "fas fa-calendar",
            up: "fas fa-arrow-up",
            down: "fas fa-arrow-down"
        }
    });
});

$('#user-not-managed-text').css('visibility', 'hidden');

$("#type").change(function () {
    switch ($("#type").val()) {
        case "shifts":
            $('#date-filter').hide('fast');
            $('#user-filter').show('fast');
            $('#deleted-filter').hide('fast');
            break;
        case "alljobs":
            $('#date-filter').hide('fast');
            $('#user-filter').hide('fast');
            $('#deleted-filter').show('fast');
            break;
        default:
            $('#date-filter').show('fast');
            $('#user-filter').show('fast');
            $('#deleted-filter').hide('fast');
            break;
    }
});

$('#date-filter').hide();
$('#deleted-filter').hide();