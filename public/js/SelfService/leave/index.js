(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#leaveTable").kendoGrid({
            excel: {
                fileName: "LeaveList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.leaves,
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
                {field: "LEAVE_CODE", title: "Leave Code"},
                {field: "LEAVE_ENAME", title: "Leave Name"},
                {field: "TOTAL_DAYS", title: "Total Days"},
                {field: "LEAVE_TAKEN", title: "Leave Taken"},
                {field: "BALANCE", title: "Available Days"},
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
            var grid = $("#leaveTable").data("kendoGrid");
            grid.saveAsExcel();
        });
    });
})(window.jQuery, window.app);
