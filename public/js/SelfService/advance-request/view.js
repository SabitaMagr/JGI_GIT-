(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var $startDate = $("#startDate");
        app.addDatePicker($startDate);
        $startDate.datepicker('setStartDate', new Date());
    });
})(window.jQuery, window.app);

angular.module("hris", [])
        .controller("advanceDetailController", function ($scope, $http) {
            $scope.allowAmt = null;
            $scope.allowTerms = null;
            $scope.salaryDeduction = null;

            var employeeId = angular.element(document.getElementById('form-employeeId')).val();
            var advanceId = angular.element(document.getElementById('form-advanceId')).val();
            var reqAmt = angular.element(document.getElementById("form-requestedAmount")).val();
            var terms = angular.element(document.getElementById("form-terms")).val();
            window.app.pullDataById(document.url, {
                action: 'pullAdvanceDetailByEmpId',
                data: {
                    'employeeId': employeeId,
                    'advanceId': advanceId
                }
            }).then(function (success) {
                $scope.$apply(function () {
                    $scope.allowAmt = success.data.allowAmt;
                    $scope.allowTerms = success.data.allowTerms;
                });
            });

            $scope.salaryDeduction = parseFloat(reqAmt) / parseFloat(terms);

        });
