(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["ID"],
                'url': document.deleteLink
            }
        };

        var actionTemplate = app.genKendoActionTemplate(actiontemplateConfig);
        var locationLinkTemplate = `
               
                `;
        var allTemplate = actionTemplate + locationLinkTemplate;

        var columns = [
            {title: "Attendance Date", columns: [
                    {field: "ATTENDANCE_DATE_AD", title: "AD", width: "100px"},
                    {field: "ATTENDANCE_DATE_BS", title: "BS", width: "100px"},
                ]},
            {field: "CUSTOMER_ENAME", title: "Customer", width: "150px"},
            {field: "LOCATION_NAME", title: "Location", width: "150px"},
            {field: "EMPLOYEE_NAME", title: "Employee", width: "150px"},
            {field: "POSTING_TYPE", title: "POSTING TYPE", width: "150px"},
            {field: ["ID"], width: "90px", title: "Action", template: allTemplate}
        ];
        var map = {
            'ATTENDANCE_DATE_AD': 'ATTENDANCE_DATE_AD',
            'ATTENDANCE_DATE_BS': 'ATTENDANCE_DATE_BS Name',
            'CUSTOMER_ENAME': 'Customer',
            'LOCATION_NAME': 'Location',
            'EMPLOYEE_NAME': 'Employee',
            'POSTING_TYPE': 'Posting Type',
        }
        app.initializeKendoGrid($table, columns);

        app.searchTable($table, ['ATTENDANCE_DATE_AD', 'ATTENDANCE_DATE_BS', 'CUSTOMER_ENAME', 'LOCATION_NAME', 'EMPLOYEE_NAME', 'POSTING_TYPEFF']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Customer Contract List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Customer Contract List.pdf');
        });

        app.pullDataById("", {}).then(function (response) {
            console.log(response);
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);