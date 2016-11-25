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
                {field: "ALLOW_HALFDAY", title: "Allow Halfday"},
                {field: "DEFAULT_DAYS", title: "Default Days"},
                {field: "CARRY_FORWARD", title: "Carry Forward"},
                {field: "CASHABLE", title: "Cashable"},
                {title: "Action"}
            ]
        }); 
    
    });   
})(window.jQuery, window.app);
