(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#appraisalStageTable").kendoGrid({
            excel: {
                fileName: "AppraisalStageList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.stages,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "STAGE_CODE", title: "Stage Code",width:100},
                {field: "STAGE_EDESC", title: "Stage Ename",width:180},
                {field: "STAGE_NDESC", title: "Stage Nname",width:180},
                {field: "ORDER_NO", title: "Order No.",width:80},
                {field: "START_DATE", title: "Start Date",width:80},
                {field: "END_DATE", title: "End Date",width:80},
                {title: "Action",width:100}
            ],
        });
        $("#export").click(function (e) {
            var grid = $("#appraisalStageTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });
})(window.jQuery);