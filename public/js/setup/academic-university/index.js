(function ($) {
    'use strict';
    $(document).ready(function () {    
        $("#academicUniversityTable").kendoGrid({
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
                {field: "ACADEMIC_UNIVERSITY_CODE", title: "Academic University Code"},
                {field: "ACADEMIC_UNIVERSITY_NAME", title: "Academic University Name"},
                {title: "Action"}
            ]
        }); 
    
    });   
})(window.jQuery, window.app);
