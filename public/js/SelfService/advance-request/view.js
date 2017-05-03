(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var $startDate = $("#startDate");
        app.datePickerWithNepali("form-advanceDate", "nepaliDate");
        $startDate.datepicker('setStartDate', new Date());
    });
})(window.jQuery, window.app);

angular.module("hris", [])
        .controller("advanceDetailController", function ($scope, $http) {
            var employeeId = angular.element(document.getElementById('form-employeeId')).val();
            var advanceId = angular.element(document.getElementById('form-advanceId')).val();
            var reqAmt = angular.element(document.getElementById("form-requestedAmount")).val();
            var terms = angular.element(document.getElementById("form-terms")).val();

            $scope.allowAmt = null;
            $scope.allowTerms = null;
            $scope.salaryDeduction = parseFloat(reqAmt) / parseFloat(terms);

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
            }, function (failure) {
                console.log("pullAdvanceDetailByEmpId", failure);
            });
            console.log(document.advanceRequestData);

            $scope.advanceRequestData = document.advanceRequestData;
            var generateVoucherFn = function () {
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.wsGenerateAdvanceVoucher, {}).then(function (response) {
                    App.unblockUI("#hris-page-content");
                    if (response.success) {
                        $scope.$apply(function () {
                            $scope.advanceRequestData.VOUCHER_NO = response.data.VOUCHER_NO;
                            $scope.voucherBtn = {
                                display: $scope.advanceRequestData.STATUS === 'AP',
                                text: ($scope.advanceRequestData.VOUCHER_NO == null) ? "Generate Voucher" : "Print Voucher",
                                fn: ($scope.advanceRequestData.VOUCHER_NO == null) ? generateVoucherFn : printVoucherFn
                            };
                            window.app.showMessage("Your Voucher has been generated.");
                        });
                    } else {
                        window.app.showMessage(response.error, 'error');
                        console.log('generateVoucherFn=>', response.error);
                    }
                }, function (error) {
                    App.unblockUI("#hris-page-content");
                    console.log('generateVoucherFn=>', error);
                });
            };
            var printVoucherFn = function () {
                alert("print");
            };

            $scope.voucherBtn = {
                display: $scope.advanceRequestData.STATUS === 'AP',
                text: ($scope.advanceRequestData.VOUCHER_NO == null) ? "Generate Voucher" : "Print Voucher",
                fn: ($scope.advanceRequestData.VOUCHER_NO == null) ? generateVoucherFn : printVoucherFn
            };





        });
