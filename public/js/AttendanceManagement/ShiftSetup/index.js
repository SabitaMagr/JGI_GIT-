(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#shiftTable").kendoGrid({
            excel: {
                fileName: "ShiftList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.shifts,
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
                {field: "SHIFT_CODE", title: "Shift Code",width:120},
                {field: "SHIFT_ENAME", title: "Shift Name",width:200},
                {field: "START_TIME", title: "Start Time",width:120},
                {field: "END_TIME", title: "End Time",width:120},
                {title: "Action",width:80}
            ]
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
        ;
        $("#export").click(function (e) {
            var grid = $("#shiftTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);
