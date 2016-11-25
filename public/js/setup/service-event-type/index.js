(function ($) {
    'use strict';
    $(document).ready(function () {
        
        $("#serviceEventTypeTable").kendoGrid({
            dataSource: {
                data: document.serviceEventTypes,
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
                {field: "SERVICE_EVENT_TYPE_CODE", title: "Service Event Type Code"},
                {field: "SERVICE_EVENT_TYPE_NAME", title: "Service Event Type Name"},
                {title: "Action"}
            ]
        }); 
    
    });   
})(window.jQuery, window.app);