(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.addDatePicker(
                $("#startDate")
                );
        
//        var submitted = false;
//        $("#advance-form").on("submit", function (e) {
//            console.log('e',e);
//            var self = this;
//            var employeeId = $("#form-employeeId").val();
//            var advanceId = $("#form-advanceId").val();
//            var requestedAmount = $("#form-requestedAmount").val();
//            var terms = $("#form-terms").val();
//            if (!submitted) {
//                e.preventDefault();
//                submitted = true;
//            } else {
//                return true;
//            }
//            window.app.pullDataById(document.url, {
//                action: 'checkAdvanceRestriction',
//                data: {
//                    'employeeId': employeeId,
//                    'advanceId': advanceId,
//                    'requestedAmount': requestedAmount,
//                    'terms': terms
//                }
//            }).then(function (success) {
//                var temp = success.data;
//
//                var parentIdReqAmt = $("#form-requestedAmount").parent(".form-group");
//                var parentIdTerms = $("#form-terms").parent(".form-group");
//
//                app.displayErrorMessage(parentIdReqAmt, temp.allowAmt, temp.errorAmt);
//                app.displayErrorMessage(parentIdTerms, temp.allowTerms, temp.errorTerms);
//                if (parseInt(temp.allowAmt) == 0 && parseInt(temp.allowTerms) == 0) {
//                    $('#advance-form').submit();
//                    
//                } else {
//                    console.log("not submitted");
//                    submitted = false;
//                }
//            });
//        });
    });
})(window.jQuery, window.app);

angular.module("hris",[])
        .controller("advanceDetailController",function($scope,$http){
            
            var advanceChange = function () {
                var advanceId = angular.element(document.getElementById('form-advanceId')).val();
                var employeeId = angular.element(document.getElementById('form-employeeId')).val();
                
                console.log(advanceId);
                console.log(employeeId);
                window.app.pullDataById(document.url, {
                action: 'pullAdvanceDetailByEmpId',
                        data: {
                        'employeeId': employeeId,
                        'advanceId': advanceId
                        }
                }).then(function (success) {
                    $scope.$apply(function () {
                        console.log(success.data);
                        $scope.allowAmt = success.data.allowAmt;
                        $scope.allowTerms = success.data.allowTerms;
                    });
                });
            };
            $scope.advanceChange = advanceChange;
            advanceChange();
})


