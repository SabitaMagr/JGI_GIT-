(function ($) {
    'use strict';
    $(document).ready(function () {    
       
        $("#serviceQuestionTable").kendoGrid({
            excel: {
                fileName: "EmployeeServiceQuestionList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.list,
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
                {field: "FIRST_NAME", title: "Employee",width:200},
                {field: "SERVICE_EVENT_TYPE_NAME", title: "Service Event Type",width:200},
                {field: "QA_DATE", title: "Question Date",width:200},
                    {title: "Action",width:100}
            ]
        }); 
        $("#export").click(function (e) {
            var grid = $("#serviceQuestionTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });   
})(window.jQuery, window.app);
