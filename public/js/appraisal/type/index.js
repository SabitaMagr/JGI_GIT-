(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#appraisalTypeTable").kendoGrid({
            excel: {
                fileName: "AppraisalTypeList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.types,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "APPRAISAL_TYPE_CODE", title: "Appraisal Type Code",width:150},
                {field: "APPRAISAL_TYPE_EDESC", title: "Appraisal Type Ename",width:180},
                {field: "APPRAISAL_TYPE_NDESC", title: "Appraisal Type Nname",width:180},
                {field: "SERVICE_TYPE_NAME", title: "Service Type Name",width:180},
                {title: "Action",width:100}
            ],
        });
        $("#export").click(function (e) {
            var grid = $("#appraisalTypeTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });
})(window.jQuery);