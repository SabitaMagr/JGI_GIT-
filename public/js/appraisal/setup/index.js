(function ($) {
    'use strict';
    $(document).ready(function () {
        console.log(document.appraisals);
        $("#appraisalSetupTable").kendoGrid({
            excel: {
                fileName: "AppraisalList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.appraisals,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "APPRAISAL_EDESC", title: "Appraisal Detail(in Eng.)",width:120},
                {field: "APPRAISAL_NDESC", title: "Appraisal Detail(in Nep.)",width:120},
                {field: "APPRAISAL_TYPE_EDESC", title: "Type",width:120},
                {field: "START_DATE", title: "Start Date",width:80},
                {field: "END_DATE", title: "End Date",width:80},
                {field: "STAGE_EDESC", title: "Current Stage",width:100},
                {title: "Action",width:90}
            ],
        });
        $("#export").click(function (e) {
            var grid = $("#appraisalSetupTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });
})(window.jQuery);