(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var $startDate = $("#form-advanceDate");
//        app.addDatePicker($startDate);
        app.datePickerWithNepali("form-advanceDate","nepaliDate");
        $startDate.datepicker('setStartDate', new Date());
        app.setLoadingOnSubmit("advanceAdvance-form");
    });
})(window.jQuery, window.app);
angular.module('hris', [])
        .controller('advanceController', function ($scope, $http) {
            $scope.employeeId = null;
            $scope.advanceId = null;

            $scope.allowAmt = null;
            $scope.allowTerms = null;
            $scope.requestedAmt = null;
            $scope.terms = null;
            $scope.salaryDeduction = null;

            $scope.advanceChange = function () {
                window.app.pullDataById(document.url, {
                    action: 'pullAdvanceDetailByEmpId',
                    data: {
                        'employeeId': $scope.employeeId,
                        'advanceId': $scope.advanceId
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        $scope.allowAmt = success.data.allowAmt;
                        $scope.allowTerms = success.data.allowTerms;
                    });
                });
            };
            $scope.requestChange = function () {
                if ($scope.requestedAmt !== null && $scope.terms !== null) {
                    $scope.salaryDeduction = parseFloat($scope.requestedAmt) / parseFloat($scope.terms);
                }
            };
            $scope.employeeChange = function () {
                window.app.floatingProfile.setDataFromRemote($scope.employeeId);
                window.app.pullDataById(document.url, {
                    action: 'pullAdvanceList',
                    data: {
                        'employeeId': $scope.employeeId
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        $scope.advanceList = success.data;
                        $scope.advanceId = $scope.advanceList[0];
                    });
                });
            };
        });



