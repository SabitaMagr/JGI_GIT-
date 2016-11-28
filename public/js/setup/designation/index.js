(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("#designationTable").kendoGrid({
            dataSource: {
                data: document.designations,
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
                {field: "DESIGNATION_CODE", title: "Designation Code"},
                {field: "DESIGNATION_TITLE", title: "Designation Name"},
                {field: "PARENT_DESIGNATION_TITLE", title: "Parent Designation"},
                {field: "BASIC_SALARY", title: "Basic Salary"},
                {title: "Action"}
            ]
        });
    });
})(window.jQuery, window.app);
