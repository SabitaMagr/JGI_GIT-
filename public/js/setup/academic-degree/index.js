(function ($) {
    'use strict';
    $(document).ready(function () {
        
        $("#academicDegreeTable").kendoGrid({
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
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "ACADEMIC_DEGREE_CODE", title: "Academic Degree Code"},
                {field: "ACADEMIC_DEGREE_NAME", title: "Academic Degree Name"},
                {field: "WEIGHT", title: "Weight"},
                {title: "Action"}
            ]
        }); 
    
    });   
})(window.jQuery, window.app);