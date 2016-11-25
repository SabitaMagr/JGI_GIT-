(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#branchTable").kendoGrid({
            dataSource: {
                data: document.branches,
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
                {field: "BRANCH_CODE", title: "Branch Code"},
                {field: "BRANCH_NAME", title: "Branch Name"},
                {field: "STREET_ADDRESS", title: "Street Address"},
                {field: "TELEPHONE", title: "Telephone"},
                {field: "EMAIL", title: "Email"},
                {title: "Action"}
            ],
            toolbar: ["excel"],
            excel: {
                fileName: "Kendo UI Grid Export.xlsx",
                proxyURL: "//demos.telerik.com/kendo-ui/service/export",
                filterable: true
            },
        });

        $('#saveAsPdfBtn').on("click", function () {
            var grid = $("#branchTable").data("kendoGrid");
            console.log(grid);
//            grid.saveAsPDF();
        });
    });
})(window.jQuery);