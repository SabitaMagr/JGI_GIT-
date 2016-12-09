(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#academicCourseTable").kendoGrid({
            excel: {
                fileName: "AcademicCourseList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.academicCourses,
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
            dataBound: gridDataBound,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "ACADEMIC_COURSE_CODE", title: "Academic Course Code",width:120},
                {field: "ACADEMIC_COURSE_NAME", title: "Academic Course Name",width:200},
                {field: "ACADEMIC_PROGRAM_NAME", title: "Academic Program Name",width:200},
                {title: "Action",width:80}
            ]
        });
        function gridDataBound(e) {
            var grid = e.sender;
            if (grid.dataSource.total() == 0) {
                var colCount = grid.columns.length;
                $(e.sender.wrapper)
                        .find('tbody')
                        .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
            }
        }
        ;
        $("#export").click(function (e) {
            var grid = $("#academicCourseTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();

    });
})(window.jQuery, window.app);