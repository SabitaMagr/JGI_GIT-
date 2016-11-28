(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#shiftTable").kendoGrid({
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
            dataBound:gridDataBound,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "SHIFT_CODE", title: "Shift Code"},
                {field: "SHIFT_ENAME", title: "Shift Name"},
                {field: "START_TIME", title: "Start Time"},
                {field: "END_TIME", title: "End Time"},
                {title: "Action"}
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
        };
    });
})(window.jQuery, window.app);
