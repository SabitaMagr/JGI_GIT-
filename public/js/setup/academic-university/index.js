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
//                {field: "ACADEMIC_UNIVERSITY_CODE", title: "Academic University Code",width:120},
                {field: "ACADEMIC_UNIVERSITY_NAME", title: "Academic University",width:400},
                {title: "Action",width:110}
            ]
        });
        
        app.searchTable('academicUniversityTable',['ACADEMIC_UNIVERSITY_NAME']);
        
        app.pdfExport(
                'academicUniversityTable',
                {
                    'ACADEMIC_UNIVERSITY_NAME': 'Academic University Name'
                }
        );
        
        $("#export").click(function (e) {
            var grid = $("#academicUniversityTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();

    });
})(window.jQuery, window.app);
