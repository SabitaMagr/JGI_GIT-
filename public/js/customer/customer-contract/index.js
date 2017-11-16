(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["CONTRACT_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["CONTRACT_ID"],
                'url': document.deleteLink
            }
        };
        var columns = [
            {field: "CUSTOMER_ENAME", title: "Customer"},
            {title: "From", columns: [
                    {field: "START_DATE_AD", title: "AD"},
                    {field: "START_DATE_BS", title: "BS"},
                ]},
            {title: "To", columns: [
                    {field: "END_DATE_AD", title: "AD"},
                    {field: "END_DATE_BS", title: "BS"},
                ]},
            {field: "IN_TIME", title: "In Time"},
            {field: "OUT_TIME", title: "Out Time"},
            {field: "WORKING_HOURS", title: "Hours"},
            {field: "WORKING_CYCLE", title: "Cycle"},
            {field: "CHARGE_TYPE", title: "Charge Type"},
            {field: "CHARGE_RATE", title: "Charge Rate"},
            {field: "REMARKS", title: "Remarks"},
            {field: ["CONTRACT_ID"], title: "Action", template: app.genKendoActionTemplate(actiontemplateConfig)}
        ];
        var map = {
            'CUSTOMER_ENAME': 'Customer Name',
            'START_DATE_AD': 'From (AD)',
            'START_DATE_BS': 'From (BS)',
            'END_DATE_AD': 'To (AD)',
            'END_DATE_BS': 'To (BS)',
            'IN_TIME': 'In Time',
            'OUT_TIME': 'Out Time',
            'WORKING_HOURS': 'Working Hours',
            'WORKING_CYCLES': 'Working Cycles',
            'CHARGE_TYPE': 'Charge Type',
            'CHARGE_RATE': 'Charge Rate',
            'REMARKS': 'Remarks',
        }
        app.initializeKendoGrid($table, columns);

        app.searchTable($table, ['CUSTOMER_ENAME']);

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