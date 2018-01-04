/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

var jobtable = $('#jobtable').DataTable({
    responsive: {
        details: {
            display: $.fn.dataTable.Responsive.display.modal({
                header: function (row) {
                    var data = row.data();
                    return "<i class=\"fa fa-briefcase fa-fw\"></i> " + data[1];
                }
            }),
            renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                tableClass: 'table'
            }),
            type: "column"
        }
    },
    columnDefs: [
        {
            targets: 0,
            className: 'control',
            orderable: false
        },
        {
            targets: 1,
            orderable: false
        },
        {
            targets: 5,
            orderable: false
        }
    ],
    order: [
        [3, 'desc']
    ],
    serverSide: true,
    ajax: {
        url: "lib/getjobhistorytable.php",
        data: function (d) {
            if ($('#show_all_checkbox').is(':checked')) {
                d.show_all = 1;
            }
        },
    }
});

$('#jobtable_filter').append("<div class=\"checkbox inblock\"><label><input type=\"checkbox\" id=\"show_all_checkbox\"> " + lang_show_all + "</label></div>");

$('#show_all_checkbox').click(function () {
    jobtable.ajax.reload();
});