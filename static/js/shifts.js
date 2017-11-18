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
            if ($('#show_btn').data("showall") == 1) {
                d.show_all = 1;
            }
        },
    }
});

$('#show_btn').click(function () {
    if ($('#show_btn').data("showall") == 1) {
        $('#show_btn').data("showall", "");
        $('#showing-all').css("display", "none");
        $('#show_btn span').text(lang_show_all_shifts);
    } else {
        $('#show_btn').data("showall", "1");
        $('#showing-all').css("display", "inline-block");
        $('#show_btn span').text(lang_show_my_shifts);
    }
    shifttable.ajax.reload();
});

$('#showing-all').css("display", "none");