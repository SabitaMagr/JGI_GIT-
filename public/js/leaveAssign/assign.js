/**
 * Created by ukesh on 9/15/16.
 */
(function ($, app) {
    'use strict';
    var datatable = $('#tableWithSearch').dataTable().api();
    var employeeId = $('#employeeId');

    employeeId.on("change", function () {
        var id = $(this).val();
        app.pullDataById(document.url, {action: 'assignList', id: id}).then(function (data) {
            console.log(data);

            datatable.clear();
            datatable.rows.add([[2, 2, 2, 2, 2, 2], [2, 2, 2, 2, 2, 2], [2, 2, 2, 2, 2, 2]]);
            datatable.draw();
        }, function (error) {
            console.log(error);

        });
    });


})(window.jQuery, window.app);