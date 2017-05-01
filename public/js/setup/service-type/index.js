(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("#serviceTypeTable").kendoGrid({
            excel: {
                fileName: "ServiceTypeList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.serviceTypes,
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
//                {field: "SERVICE_TYPE_CODE", title: "Service Type Code",width:120},
                {field: "SERVICE_TYPE_NAME", title: "Service Type",width:400},
                {title: "Action",width:110}
            ]
        });
        $("#export").click(function (e) {
            var grid = $("#serviceTypeTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);