(function ($) {
    'use strict';

    $(document).ready(function () {
        console.log(document.issue);
        $("#assetIssueTable").kendoGrid({
            excel: {
                fileName: "AssetIssueList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.issue,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "ASSET_EDESC", title: "Asset", width: 120},
                {field: "FIRST_NAME", title: "Employee ", width: 140},
                {field: "ISSUE_DATE", title: "Issue Date", width: 140},
                {field: "QUANTITY", title: "Quantity ", width: 140},
                {field: "RETURN_DATE", title: "ReturnDate", width: 130},
                {title: "Action", width: 120}
            ],
        });
        $("#export").click(function (e) {
            var grid = $("#assetIssueTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });

})(window.jQuery);