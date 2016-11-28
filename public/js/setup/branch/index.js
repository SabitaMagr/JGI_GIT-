(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#branchTable").kendoGrid({
            dataSource: {
                data: document.branches,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "BRANCH_CODE", title: "Branch Code"},
                {field: "BRANCH_NAME", title: "Branch Name"},
                {field: "STREET_ADDRESS", title: "Street Address"},
                {field: "TELEPHONE", title: "Telephone"},
                {field: "EMAIL", title: "Email"},
                {title: "Action"}
            ],
        });

    });
})(window.jQuery);