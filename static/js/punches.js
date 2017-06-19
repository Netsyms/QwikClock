$('#punchtable').DataTable({
    responsive: {
        details: {
            display: $.fn.dataTable.Responsive.display.modal({
                header: function (row) {
                    var data = row.data();
                    return "<i class=\"fa fa-clock-o fa-fw\"></i> " + data[1];
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
            targets: 3,
            orderable: false
        }
    ],
    order: [
        [1, 'desc']
    ],
    serverSide: true,
    ajax: {
        url: "lib/getpunchtable.php"
    }
});