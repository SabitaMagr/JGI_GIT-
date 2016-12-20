(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#academicCourseTable").kendoGrid({

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
        };
        $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Academic Course Code"},
                        {value: "Academic Course Name"},
                        {value: "Academic Program Name"}
                    ]
                }];
            var dataSource = $("#academicCourseTable").data("kendoGrid").dataSource;
            var filteredDataSource = new kendo.data.DataSource({
                data: dataSource.data(),
                filter: dataSource.filter()
            });

            filteredDataSource.read();
            var data = filteredDataSource.view();
            
            for (var i = 0; i < data.length; i++) {
                var dataItem = data[i];
                rows.push({
                    cells: [
                        {value: dataItem.ACADEMIC_COURSE_CODE},
                        {value: dataItem.ACADEMIC_COURSE_NAME},
                        {value: dataItem.ACADEMIC_PROGRAM_NAME}
                    ]
                });
            }
            excelExport(rows);
            e.preventDefault();
        });
        
        function excelExport(rows) {
            var workbook = new kendo.ooxml.Workbook({
                sheets: [
                    {
                        columns: [
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true}
                        ],
                        title: "Academic Course",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AcademicCourseList.xlsx"});
        }
        window.app.UIConfirmations();

    });
})(window.jQuery, window.app);