/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */


$(function () {
    $('#start').datetimepicker({
        format: "ddd MMMM D YYYY h:mm a",
        useCurrent: false,
        icons: {
            time: "fas fa-clock",
            date: "fas fa-calendar",
            up: "fas fa-arrow-up",
            down: "fas fa-arrow-down"
        }
    });
    $('#end').datetimepicker({
        format: "ddd MMMM D YYYY h:mm a",
        useCurrent: false,
        icons: {
            time: "fas fa-clock",
            date: "fas fa-calendar",
            up: "fas fa-arrow-up",
            down: "fas fa-arrow-down"
        }
    });
});