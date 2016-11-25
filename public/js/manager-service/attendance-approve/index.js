(function ($) {
    'use strict';
    $(document).ready(function () {    

        $("#attendanceApproveTable").kendoGrid({
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
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "FIRST_NAME", title: "Employee Name"},
                {field: "ATTENDANCE_DT", title: "Attendance Date"},
                {field: "IN_TIME", title: "Check In"},
                {field: "OUT_TIME", title: "Check Out"},
                {field: "IN_REMARKS", title: "Late In Reason"},
                {field: "OUT_REMARKS", title: "Late Out Reason"},
                {field: "YOUR_ROLE", title: "Your Role"},
                {title: "Action"}
            ]
        });    
    });   
})(window.jQuery, window.app);
