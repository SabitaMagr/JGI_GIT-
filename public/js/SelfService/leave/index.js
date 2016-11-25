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
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "LEAVE_CODE", title: "Leave Code"},
                {field: "LEAVE_ENAME", title: "Leave Name"},
                {field: "TOTAL_DAYS", title: "Total Days"},
                {field: "LEAVE_TAKEN", title: "Leave Taken"},
                {field: "BALANCE", title: "Available Days"},
            ]
        });    
    });   
})(window.jQuery, window.app);
