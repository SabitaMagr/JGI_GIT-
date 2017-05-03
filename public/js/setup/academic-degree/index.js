(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#academicDegreeTable").kendoGrid({
            excel: {
                fileName: "AcademicDegreeList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.academicDegrees,
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
//                {field: "ACADEMIC_DEGREE_CODE", title: "Academic Degree Code",width:120},
                {field: "ACADEMIC_DEGREE_NAME", title: "Academic Degree",width:300},
                {field: "WEIGHT", title: "Weight",width:100},
                {title: "Action",width:110}
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
            var grid = $("#academicDegreeTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();

    });
})(window.jQuery, window.app);