(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#academicUniversityTable").kendoGrid({
            excel: {
                fileName: "AcademicUniversityList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.academicUniversities,
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
                {field: "ACADEMIC_UNIVERSITY_CODE", title: "Academic University Code",width:120},
                {field: "ACADEMIC_UNIVERSITY_NAME", title: "Academic University Name",width:200},
                {title: "Action",width:50}
            ]
        });
        $("#export").click(function (e) {
            var grid = $("#academicUniversityTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();

    });
})(window.jQuery, window.app);
