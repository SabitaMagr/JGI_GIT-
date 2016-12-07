(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#academicProgramTable").kendoGrid({
            excel: {
                fileName: "AcademicProgramList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.academicPrograms,
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
                {field: "ACADEMIC_PROGRAM_CODE", title: "Academic Program Code",width:120},
                {field: "ACADEMIC_PROGRAM_NAME", title: "Academic Program Name",width:200},
                {title: "Action",width:50}
            ]
        });
        $("#export").click(function (e) {
            var grid = $("#academicProgramTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();

    });
})(window.jQuery, window.app);
