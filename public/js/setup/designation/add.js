/**
 * Created by punam on 9/28/16.
 */
(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var scopeArea = function(){
            var parentDesignation = $("#form-parentDesignation").val();
            if(parentDesignation=="" || parentDesignation=="-1"){
                $("#scopeArea").slideUp();
                $("input[name=withinBranch]").attr("disabled","disabled");
                $("input[name=withinDepartment]").attr("disabled","disabled");
            }else{
                $("#scopeArea").slideDown();
                $("input[name=withinBranch]").removeAttr("disabled");
                $("input[name=withinDepartment]").removeAttr("disabled");
            }
        };
        $("#form-parentDesignation").on("change",scopeArea);
        scopeArea();
        
        $("#designationTable").kendoGrid({
            dataSource: {
                data: document.designations,
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
                {field: "DESIGNATION_CODE", title: "Designation Code"},
                {field: "DESIGNATION_TITLE", title: "Designation Name"},
                {field: "PARENT_DESIGNATION", title: "Parent Designation"},
                {field: "BASIC_SALARY", title: "Basic Salary"},
                {title: "Action"}
            ]
        });
    });
})(window.jQuery,window.app);
