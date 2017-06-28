
$(document).ready(function () {
    /* Fade out alerts */
    $(".alert .close").click(function (e) {
        $(this).parent().fadeOut("slow");
    });

    try {
        window.history.replaceState("", "", getniceurl());
        if (window.location.hash) {
            document.getElementById(window.location.hash.replace("#", "")).scrollIntoView();
        }
    } catch (ex) {

    }
});


/*
 * Remove feedback params from the URL so they don't stick around too long
 */
function getniceurl() {
    var url = window.location.search + window.location.hash;
    url = url.substring(url.lastIndexOf("/") + 1);
    url = url.replace(/&?msg=([^&]$|[^&]*)/i, "");
    return url;
}