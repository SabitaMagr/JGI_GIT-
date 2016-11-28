(function ($) {
    'use strict';
    $(document).ready(function () {    
        
        $("#leaveTable").kendoGrid({
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
            dataBound:gridDataBound,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "LEAVE_CODE", title: "Leave Code"},
                {field: "LEAVE_ENAME", title: "Leave Name"},
                {field: "ALLOW_HALFDAY", title: "Allow Halfday"},
                {field: "DEFAULT_DAYS", title: "Default Days"},
                {field: "CARRY_FORWARD", title: "Carry Forward"},
                {field: "CASHABLE", title: "Cashable"},
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
