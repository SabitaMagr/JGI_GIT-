(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("#salaryReviewTable").kendoGrid({
            excel: {
                fileName: "BranchList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.salaryDetails,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "FIRST_NAME", title: "Employee", width: 100},
                {field: "OLD_AMOUNT", title: "Old Amount", width: 200},
                {field: "NEW_AMOUNT", title: "New Amount", width: 180},
                {field: "EFFECTIVE_DATE", title: "Effective Date", width: 100},
                {field: "JOB_HISTORY_ID", title: "JOb History Id", width: 140},
                {title: "Action", width: 100}
            ],
        });

    });
})(window.jQuery, window.app);