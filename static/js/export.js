$("#genrptbtn").click(function () {
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
        useCurrent: false
    });
    $('#enddate').datetimepicker({
        format: "MMM D YYYY"/*"YYYY-M-DTH:m"*/,
        useCurrent: true
    });
});

$('#user-not-managed-text').css('visibility', 'hidden');