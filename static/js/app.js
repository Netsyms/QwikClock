/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

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
try {
    window.history.replaceState("", "", getniceurl());
} catch (ex) {

}