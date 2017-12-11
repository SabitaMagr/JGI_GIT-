(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');

        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["PAY_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["PAY_ID"],
                'url': document.deleteLink
            }
        };
        app.initializeKendoGrid($table, [
            {field: "PAY_CODE", title: "Pay Code"},
            {field: "PAY_EDESC", title: "Pay Title"},
            {field: "PAY_TYPE", title: "Pay Type"},
            {field: "IS_MONTHLY_DETAIL", title: "Is Monthly"},
            {field: "PRIORITY_INDEX", title: "Priority No"},
            {field: "PAY_ID", title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ]);

        app.searchTable($table, ["PAY_EDESC", "PAY_TYPE", "IS_MONTHLY_DETAIL"]);
        var map = {
            "PAY_ID": "Pay Id",
            "PAY_CODE": "Pay Code",
            "PAY_EDESC": "Pay Title",
            "PAY_TYPE": "Pay Type",
            "IS_MONTHLY_DETAIL": "Is Monthly",
            "PRIORITY_INDEX": "Priority No",
        };

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Pay List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Pay List');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);