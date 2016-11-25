(function ($) {
    'use strict';
    $(document).ready(function () {    
       
        $("#flatValueTable").kendoGrid({
            dataSource: {
                data: document.flatValue,
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
                {field: "FLAT_CODE", title: "Attendance Date"},
                {field: "FLAT_EDESC", title: "Check In"},
                {field: "FLAT_LDESC", title: "Check Out"},
                {field: "SHOW_AT_RULE", title: "Late In Reason"},
                {title: "Action"}
            ]
        });    
    });   
})(window.jQuery, window.app);
