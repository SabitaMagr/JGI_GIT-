(function ($) {
    'use strict';
    $(document).ready(function () {    
       
        $("#leaveApproveTable").kendoGrid({
            excel: {
                fileName: "LeaveRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.leaveApprove,
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
                {field: "FIRST_NAME", title: "Employee Name",width:200},
                {field: "LEAVE_ENAME", title: "Leave Name",width:120},
                {field: "APPLIED_DATE", title: "Requested Date",width:140},
                {field: "START_DATE", title: "From Date",width:100},
                {field: "END_DATE", title: "To Date",width:90},
                {field: "NO_OF_DAYS", title: "Duration",width:100},
                {field: "YOUR_ROLE", title: "Your Role",width:120},
                {title: "Action",width:70}
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
        $("#export").click(function (e) {
            var grid = $("#leaveApproveTable").data("kendoGrid");
            grid.saveAsExcel();
        });
    });   
})(window.jQuery, window.app);
