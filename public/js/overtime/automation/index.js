(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $("#overtimeAutomationTable");
        var $rowTemplate = $("#rowTemplate");
        var data = document.compulsoryOTList;
        $table.kendoGrid({
            dataSource: {
                data: data,
                pageSize: 20
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: {
                input: true,
                numeric: false
            },
            rowTemplate: kendo.template($rowTemplate.html()),
            columns: [
                {field: "EARLY_OVERTIME_HR", title: "Early Overtime Hour", width: 150},
                {field: "LATE_OVERTIME_HR", title: "Late Overtime Hour", width: 150},
                {field: "START_DATE", title: "Start Date", width: 150},
                {field: "END_DATE", title: "End Date", width: 150},
                {title: "Action", width: 80}
            ]
        });


        app.pdfExport(
                'overtimeAutomationTable',
                {
                    'EARLY_OVERTIME_HR': 'Early Overtime Hour',
                    'LATE_OVERTIME_HR': 'Late Overtime Hour',
                    'START_DATE': 'Start Date',
                    'END_DATE': 'End Date'
                }
        );

        app.UIConfirmations();

    });
})(window.jQuery, window.app);