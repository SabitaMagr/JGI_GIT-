(function ($) {
    'use strict';
    $(document).ready(function () {    
     
        $("#leaveRequestTable").kendoGrid({
            dataSource: {
                data: document.leaveRequest,
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
                {field: "LEAVE_CODE", title: "Leave Code"},
                {field: "LEAVE_ENAME", title: "Leave Name"},
                {field: "FROM_DATE", title: "From Date"},
                {field: "TO_DATE", title: "To Date"},
                {field: "NO_OF_DAYS", title: "Duration"},
                {field: "STATUS", title: "Status"},
                {title: "Action"}
            ]
        });    
    });   
})(window.jQuery, window.app);
