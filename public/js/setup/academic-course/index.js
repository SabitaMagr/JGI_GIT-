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
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "ACADEMIC_COURSE_CODE", title: "Academic Course Code"},
                {field: "ACADEMIC_COURSE_NAME", title: "Academic Course Name"},
                {field: "ACADEMIC_PROGRAM_NAME", title: "Academic Program Name"},
                {title: "Action"}
            ]
        }); 
    
    });   
})(window.jQuery, window.app);