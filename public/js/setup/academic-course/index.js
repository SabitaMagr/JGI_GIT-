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
            dataBound:gridDataBound,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "ACADEMIC_COURSE_CODE", title: "Academic Course Code"},
                {field: "ACADEMIC_COURSE_NAME", title: "Academic Course Name"},
                {field: "ACADEMIC_PROGRAM_NAME", title: "Academic Program Name"},
                {title: "Action"}
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
    
    });   
})(window.jQuery, window.app);