
(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#serviceTypeTable');
        var editAction = '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:SERVICE_TYPE_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>';
        var deleteAction = '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:SERVICE_TYPE_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "SERVICE_TYPE_NAME", title: "Service Type", width: 400},
        ], "ServiceType List.xlsx");

        app.searchTable('serviceTypeTable', ['SERVICE_TYPE_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'SERVICE_TYPE_NAME': 'Service Type',
            }, 'ServiceType List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'SERVICE_TYPE_NAME': 'Service Type',
            }, 'ServiceType List');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);