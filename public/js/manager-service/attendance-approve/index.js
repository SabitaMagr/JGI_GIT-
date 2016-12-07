(function ($) {
    'use strict';
    $(document).ready(function () {    

        $("#attendanceApproveTable").kendoGrid({
            excel: {
                fileName: "AttendanceRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.attendanceApprove,
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
                {field: "ATTENDANCE_DT", title: "Attendance Date",width:180},
                {field: "IN_TIME", title: "Check In",width:120},
                {field: "OUT_TIME", title: "Check Out",width:140},
                {field: "IN_REMARKS", title: "Late In Reason",width:170},
                {field: "OUT_REMARKS", title: "Late Out Reason",width:180},
                {field: "YOUR_ROLE", title: "Your Role",width:140},
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
        };
        $("#export").click(function (e) {
            var grid = $("#attendanceApproveTable").data("kendoGrid");
            grid.saveAsExcel();
        });
    });   
})(window.jQuery, window.app);
