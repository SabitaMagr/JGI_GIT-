// (function ($, app) {
//     'use strict';
//     $(document).ready(function () {
//         var generateBtn = $('#generateBtn');
//         var employeeCb = $('#employeeId');
//
//         generateBtn.on('click', function (e) {
//             app.pullDataById(document.url, {
//                 action: 'generataMonthlySheet',
//                 data: {employee: employeeCb.val()}
//             }).then(function (success) {
//                 console.log(success);
//             }, function (failure) {
//                 console.log(failure);
//             });
//         });
//
//     });
// })(window.jQuery, window.app);


angular.module('hris', [])
        .controller('generateController', function ($scope) {
            var generateBtn = angular.element(document.querySelector('#generateBtn'));

            $scope.rules = document.rules;
            $scope.employeeList = document.employeeList;
            $scope.branches = document.branches;
            $scope.fiscalYears = document.fiscalYears;
            $scope.months = [];
            $scope.payRollGeneratedMonths = [];

            $scope.employeeId;
            $scope.branchId;
            $scope.fiscalYearId;
            $scope.monthId;
            $scope.employeeRuleValues = {};

            generateBtn.on('click', function (e) {
                if (((typeof $scope.monthId === 'undefined') || ($scope.monthId === null))) {
                    console.log("month not defined");
                    window.app.successMessage("Month not defined!");
                    return;
                }
                console.log("parameters", {
                    month: $scope.monthId,
                    branch: ((typeof $scope.branchId === 'undefined') || ($scope.branchId === null)) ? -1 : $scope.branchId,
                    employee: (($scope.employeeId === null) || (typeof $scope.employeeId === 'undefined')) ? -1 : $scope.employeeId,
                });
                $scope.pullMonthlySheet({
                    month: $scope.monthId,
                    branch: ((typeof $scope.branchId === 'undefined') || ($scope.branchId === null)) ? -1 : $scope.branchId,
                    employee: (($scope.employeeId === null) || (typeof $scope.employeeId === 'undefined')) ? -1 : $scope.employeeId,
                });
            });

            $scope.pullMonthlySheet = function (reqParams) {
                window.app.pullDataById(document.url, {
                    action: 'generataMonthlySheet',
                    data: reqParams
                }).then(function (success) {
                    $scope.$apply(function () {
                        console.log("generateMonthlySheet", success);
                        $scope.employeeRuleValues = success.data;
                    });
                }, function (failure) {
                    console.log(failure);
                });

            };

            $scope.changeBranch = function () {
                window.app.pullDataById(document.url, {
                    action: 'pullEmployeeList',
                    data: {
                        'employeeId': -1,
                        'branchId': ($scope.branchId == null) ? -1 : $scope.branchId,
                        'departmentId': -1,
                        'designationId': -1,
                        'positionId': -1,
                        'serviceTypeId': -1,
                        'serviceEventTypeId': -1
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        console.log("pullEmployeeListByBranchId", success.data);
                        var empList = success.data;
                        $scope.employeeList = [];
                        for (var i = 0; i < empList.length; i++) {
                            $scope.employeeList[empList[i]['employeeId']] = empList[i]['firstName'];
                        }
                    });
                }, function (failure) {
                    console.log(failure);
                });
            };

            $scope.changeYear = function () {
                console.log("year changed", $scope.fiscalYearId);
                if ($scope.fiscalYearId != null) {
                    window.app.pullDataById(document.url, {
                        action: 'pullMonthsByFiscalYear',
                        data: {
                            'fiscalYearId': $scope.fiscalYearId,
                        }
                    }).then(function (success) {
                        console.log("pullMonthsByFiscalYear res", success);
                        $scope.$apply(function () {
                            $scope.months = success.data;
                        });

                    }, function (failure) {
                        console.log("pullMonthsByFiscalYear fail", failure);

                    });
                }

            };

            $scope.fetchPayRollGeneratedMonths = function () {
                window.app.pullDataById(document.url, {
                    action: 'pullPayRollGeneratedMonths',
                    data: {
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        console.log("pullPayRollGeneratedMonths res", success.data);
                        $scope.payRollGeneratedMonths = success.data;
                    });
                }, function (failure) {
                    console.log("pullPayRollGeneratedMonths fail", failure);
                });
            };
            $scope.fetchPayRollGeneratedMonths();

            $scope.viewMonthlySheet = function (monthId) {
                $scope.pullMonthlySheet({
                    month: monthId,
                    branch: ((typeof $scope.branchId === 'undefined') || ($scope.branchId === null)) ? -1 : $scope.branchId,
                    employee: (($scope.employeeId === null) || (typeof $scope.employeeId === 'undefined')) ? -1 : $scope.employeeId,
                });
            };

        });