(function ($) {
    'use strict';
    $(document).ready(function () {    
       
        $("#holidayTable").kendoGrid({
            dataSource: {
                data: document.holidays,
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
                {field: "HOLIDAY_CODE", title: "Holiday Code"},
                {field: "HOLIDAY_ENAME", title: "Holiday Name"},
                {field: "START_DATE", title: "Start Date"},
                {field: "END_DATE", title: "End Date"},
                {field: "HALF_DAY", title: "Half Day"},               
            ]
        });    
    });   
})(window.jQuery, window.app);
