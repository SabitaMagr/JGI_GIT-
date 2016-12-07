(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#branchTable").kendoGrid({
            excel: {
                fileName: "BranchList.xlsx",
                filterable: true,
                allPages: true
            },
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
                {field: "BRANCH_CODE", title: "Branch Code",width:100},
                {field: "BRANCH_NAME", title: "Branch Name",width:200},
                {field: "STREET_ADDRESS", title: "Street Address",width:180},
                {field: "TELEPHONE", title: "Telephone",width:100},
                {field: "EMAIL", title: "Email",width:140},
                {title: "Action",width:100}
            ],
        });
        $("#export").click(function (e) {
            var grid = $("#branchTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });
})(window.jQuery);