(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#departmentTable").kendoGrid({
            dataSource: {
                data: document.departments,
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
                {field: "DEPARTMENT_CODE", title: "Department Code"},
                {field: "DEPARTMENT_NAME", title: "Department Name"},
                {title: "Action"}
            ]
        });

    });
})(window.jQuery);