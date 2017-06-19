function setClock() {
    $.getJSON("action.php", {
        action: "gettime"
    }, function (resp) {
        if (resp.status == "OK") {
            $('#server_time').text(resp.time);
            $('#server_date').text(resp.date);
            var seconds = resp.seconds * 1;
            var interval = 60 - seconds;
            console.log(interval);
            if (interval > 5) {
                interval = 5;
            }
            console.log(interval);
            console.log((((seconds + interval) / 60) * 100));
            $('#seconds_bar div').animate({
                width: (((seconds + interval) / 60) * 100) + "%"
            }, 1000 * interval, "linear", function () {
                if (interval < 5) {
                    $('#seconds_bar div').animate({
                        width: "0%"
                    }, 1000, "linear", function () {
                        $('#seconds_bar div').animate({
                            width: (((5 - interval - 1) / 60) * 100) + "%"
                        }, 1000 * (5 - interval - 1), "linear");
                    });
                }
            });
        }
    });
}

$(document).ready(function () {
    setClock();
    setInterval(setClock, 5000);
});