(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#departmentTable").kendoGrid({
            excel: {
                fileName: "DepartmentList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.departments,
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
                {field: "DEPARTMENT_CODE", title: "Department Code",width:120},
                {field: "DEPARTMENT_NAME", title: "Department Name",width:200},
                {title: "Action",width:50}
            ]
        });
        $("#export").click(function (e) {
            var grid = $("#departmentTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();

    });
})(window.jQuery);