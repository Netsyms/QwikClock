var shifttable = $('#shifttable').DataTable({
    responsive: {
        details: {
            display: $.fn.dataTable.Responsive.display.modal({
                header: function (row) {
                    var data = row.data();
                    return "<i class=\"fa fa-calendar fa-fw\"></i> " + data[1];
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
        [2, 'desc']
    ],
    serverSide: true,
    ajax: {
        url: "lib/getshifttable.php",
        data: function (d) {
            if ($('#show_all_checkbox').is(':checked')) {
                d.show_all = 1;
            }
        },
    }
});

$('#shifttable_filter').append("<div class=\"checkbox inblock\"><label><input type=\"checkbox\" id=\"show_all_checkbox\"> " + lang_show_all_shifts + "</label></div>");

$('#show_all_checkbox').click(function () {
    shifttable.ajax.reload();
});