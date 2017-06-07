(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#shiftAdjustmentTable").kendoGrid({
            excel: {
                fileName: "ShiftAdjustList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.shiftsAdjust,
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
            dataBound: gridDataBound,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "ADJUSTMENT_START_DATE", title: "Start Date", width: 120},
                {field: "ADJUSTMENT_END_DATE", title: "End Date", width: 130},
                {field: "START_TIME", title: "Start Time", width: 120},
                {field: "END_TIME", title: "End Time", width: 120},
                {title: "Action", width: 110}
            ]
        });

        app.searchTable('shiftAdjustmentTable', ['ADJUSTMENT_START_DATE', 'ADJUSTMENT_END_DATE', 'START_TIME', 'END_TIME']);

        app.pdfExport(
                'shiftAdjustmentTable',
                {
                    'ADJUSTMENT_START_DATE': ' StartDate',
                    'ADJUSTMENT_END_DATE': 'End Date',
                    'START_TIME': 'Start Time',
                    'END_TIME': 'End Time'
                }
        );

        $("#export").click(function (e) {
            var grid = $("#shiftAdjustmentTable").data("kendoGrid");
            grid.saveAsExcel();
        });



        function gridDataBound(e) {
            var grid = e.sender;
            if (grid.dataSource.total() == 0) {
                var colCount = grid.columns.length;
                $(e.sender.wrapper)
                        .find('tbody')
                        .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
            }
        }




        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);
