(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select#form-transportType').select2();
        app.startEndDatePicker('fromDate', 'toDate');
        /* prevent past event post */
        $('#fromDate').datepicker("setStartDate", new Date());
        $('#toDate').datepicker("setStartDate", new Date());
        /* end of  prevent past event post */
        
        var inputFieldId = "form-travelCode";
        var formId = "travelRequest-form";
        var tableName =  "HRIS_EMPLOYEE_TRAVEL_REQUEST";
        var columnName = "TRAVEL_CODE";
        var checkColumnName = "TRAVEL_ID";
        var selfId = $("#travelId").val();
        if (typeof(selfId) == "undefined"){
            selfId='R';
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
    });
})(window.jQuery, window.app);

angular.module('hris',[])
        .controller("advanceRequestController",function($scope,$http){
            $scope.travelSubstitute =false;
            $scope.viewSubstituteEmployee = function(substituteEmployee){
                console.log(substituteEmployee);
                if(substituteEmployee==1){
                    $scope.travelSubstitute = true;
                }else{
                    $scope.travelSubstitute =false;
                }
            }
            
            $scope.printDiv = function(divName) {
                var printContents = document.getElementById(divName).innerHTML;
                var popupWin = window.open('', '_blank', 'width=1000,height=500,toolbar=0,scrollbars=0,status=0');
                popupWin.document.open();
                popupWin.document.write('<html><head><link rel="stylesheet" type="text/css" href="'+document.urlCss+'" /></head><body onload="window.print()">' + printContents + '</body></html>');
                popupWin.document.close();
              }
              $scope.hgtRecommender = 14;
              $scope.hgtApprover = 14;
              
            $scope.recommenderView = function(event){
                if(event){
                    $scope.signRecommender=true;
                }else{
                    $scope.signRecommender=false;
                }
            }
            $scope.approverView = function(event){
                if(event){
                    $scope.signApprover=true;
                }else{
                    $scope.signApprover=false;
                }
            }
}); 