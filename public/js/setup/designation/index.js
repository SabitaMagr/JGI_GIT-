(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("#designationTable").kendoGrid({
            excel: {
                fileName: "DesignationList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.designations,
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
                {field: "DESIGNATION_CODE", title: "Designation Code",width:120},
                {field: "DESIGNATION_TITLE", title: "Designation Name",width:200},
                {field: "PARENT_DESIGNATION_TITLE", title: "Parent Designation",width:200},
                {field: "BASIC_SALARY", title: "Basic Salary",width:120},
                {title: "Action",width:100}
            ]
        });
        $("#export").click(function (e) {
            var grid = $("#designationTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);
