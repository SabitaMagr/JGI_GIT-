(function ($) {
    'use strict';
    $(document).ready(function () {
        console.log(document.headings);
        $("#appraisalHeadingTable").kendoGrid({
            excel: {
                fileName: "AppraisalHeadingList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.headings,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "HEADING_CODE", title: "Heading Code",width:80},
                {field: "HEADING_EDESC", title: "Heading Name (in Eng.)",width:120},
                {field: "HEADING_NDESC", title: "Heading Name (in Nep.)",width:120},
                {field: "PERCENTAGE", title: "Percentage",width:100},
                {field: "APPRAISAL_TYPE_EDESC", title: "Appraisal Type Name",width:100},
                {title: "Action",width:100}
            ],
        });
        $("#export").click(function (e) {
            var grid = $("#appraisalHeadingTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });
})(window.jQuery);