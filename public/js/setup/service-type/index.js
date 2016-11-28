(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $("#serviceTypeTable").kendoGrid({
            dataSource: {
                data: document.serviceTypes,
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
                {field: "SERVICE_TYPE_CODE", title: "Service Type Code"},
                {field: "SERVICE_TYPE_NAME", title: "Service Type Name"},
                {title: "Action"}
            ]
        });
    });
})(window.jQuery,window.app);