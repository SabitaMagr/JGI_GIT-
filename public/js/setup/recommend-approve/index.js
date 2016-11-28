(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#recommendApproveTable").kendoGrid({
            dataSource: {
                data: document.recommendApproves,
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
                {field: "FIRST_NAME", title: "Employee Name"},
                {field: "FIRST_NAME_R", title: "Recommender"},
                {field: "FIRST_NAME_A", title: "Approver"},
                {title: "Action"}
            ]
        });

    });
})(window.jQuery);