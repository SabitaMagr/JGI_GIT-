(function ($) {
    'use strict';
    $(document).ready(function () {
        console.log(document.appraisals);
        $("#appraisalListTable").kendoGrid({
            excel: {
                fileName: "AppraisalList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.appraisals,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "FULL_NAME", title: "Employee",width:200},
                {field: "APPRAISAL_EDESC", title: "Appraisal",width:150},
                {field: "APPRAISAL_TYPE_EDESC", title: "Appraisal Type",width:120},
                {field: "STAGE_EDESC", title: "Current Status",width:100},
                {field: "START_DATE", title: "Start Date",width:80},
                {field: "END_DATE", title: "End Date",width:80},
                {title: "Action",width:90}
            ],
        });
        
        app.searchTable('appraisalListTable',['FULL_NAME','APPRAISAL_EDESC','APPRAISAL_TYPE_EDESC','STAGE_EDESC','START_DATE','END_DATE']);
        app.pdfExport(
                'appraisalListTable',
                {
                    'FULL_NAME': 'Name',
                    'APPRAISAL_EDESC': 'Appraisal',
                    'APPRAISAL_TYPE_EDESC': 'Appraisal Type',
                    'STAGE_EDESC': 'Current Status',
                    'START_DATE': 'Start Date',
                    'END_DATE': 'End Date',                    
                });
        
        $("#export").click(function (e) {
            var grid = $("#appraisalListTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });
})(window.jQuery);

angular.module("hris",[])
        .controller("appraisalList",function($scope,$http,$window){
            $scope.msg =  $window.localStorage.getItem("msg");
            if($window.localStorage.getItem("msg")){
                window.toastr.success($scope.msg, "Notifications");
            }
            $window.localStorage.removeItem("msg");
});