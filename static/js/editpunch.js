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