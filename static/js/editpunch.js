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
        data.q = $("#user").val();
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
    }
};

$("#user").easyAutocomplete(options);


$(function () {
    $('#in').datetimepicker({
        format: "ddd MMMM D YYYY h:mm a",
        useCurrent: false
    });
    $('#out').datetimepicker({
        format: "ddd MMMM D YYYY h:mm a",
        useCurrent: false
    });
});