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
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "SHIFT_CODE", title: "Shift Code"},
                {field: "SHIFT_ENAME", title: "Shift Name"},
                {field: "START_TIME", title: "Start Time"},
                {field: "END_TIME", title: "End Time"},
                {title: "Action"}
            ]
        }); 
    
    });   
})(window.jQuery, window.app);
