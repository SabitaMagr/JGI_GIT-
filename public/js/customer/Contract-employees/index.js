(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');

        var actiontemplateConfig = {
            view: {
                'params': ["CONTRACT_ID"],
                'url': document.viewLink
            },
            update: {
            },
            delete: {
            }
        };

        var columns = [
            {field: "CUSTOMER_ENAME", title: "Customer Name"},
            {field: "CONTRACT_NAME", title: "Contract Name"},
            {field: ["CONTRACT_ID"], title: "Action", template: app.genKendoActionTemplate(actiontemplateConfig)}
        ];
        var map = {
            'CUSTOMER_ENAME': 'Customer Name',
            'CONTRACT_NAME': 'Contract Name',
        }
        app.initializeKendoGrid($table, columns);

        app.searchTable($table, ['CUSTOMER_ENAME', 'CONTRACT_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Customer Contract List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Customer Contract List.pdf');
        });

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);