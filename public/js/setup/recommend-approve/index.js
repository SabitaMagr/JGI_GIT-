(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#recommendApproveTable").kendoGrid({
            excel: {
                fileName: "RecommenderApproverList.xlsx",
                filterable: true,
                allPages: true
            },
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
                {field: "FIRST_NAME", title: "Employee Name",width:200},
                {field: "FIRST_NAME_R", title: "Recommender",width:200},
                {field: "FIRST_NAME_A", title: "Approver",width:200}
            ]
        });
        $("#export").click(function (e) {
            var grid = $("#recommendApproveTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();

    });
})(window.jQuery);