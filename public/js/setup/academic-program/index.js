(function ($) {
    'use strict';
    $(document).ready(function () {    
        
        $("#academicProgramTable").kendoGrid({
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
                {field: "ACADEMIC_PROGRAM_CODE", title: "Academic Program Code"},
                {field: "ACADEMIC_PROGRAM_NAME", title: "Academic Program Name"},
                {title: "Action"}
            ]
        }); 
    
    });   
})(window.jQuery, window.app);
