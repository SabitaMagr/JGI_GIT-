(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
    });
})(window.jQuery, window.app);


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
                $this = $(e.target);
                var regenerateFlag = ($this.attr('regenerateFlag') == "true");
                if (((typeof $scope.monthId === 'undefined') || ($scope.monthId === null))) {
                    console.log("month not defined");
                    window.app.successMessage("Month not defined!");
                    return;
                }
                console.log("parameters", {
                    month: $scope.monthId,
                    branch: ((typeof $scope.branchId === 'undefined') || ($scope.branchId === null)) ? -1 : $scope.branchId,
                    employee: (($scope.employeeId === null) || (typeof $scope.employeeId === 'undefined')) ? -1 : $scope.employeeId,
                    regenerateFlag: regenerateFlag
                });
                $scope.pullMonthlySheet({
                    month: $scope.monthId,
                    branch: ((typeof $scope.branchId === 'undefined') || ($scope.branchId === null)) ? -1 : $scope.branchId,
                    employee: (($scope.employeeId === null) || (typeof $scope.employeeId === 'undefined')) ? -1 : $scope.employeeId,
                    regenerateFlag: regenerateFlag
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

                        initializeHeaders($scope.rules);
                        initializeDatas($scope.rules, $scope.employeeRuleValues);
                        initializekendoGrid(headers, datas);
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
                } else {
                    $scope.months = [];
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
                    regenerateFlag: false
                });
            };

            $scope.viewMonthlySheetIfAvailable = function () {
                generateBtn.text("Generate");
                generateBtn.attr("regenerateFlag", false);
                for (var i in $scope.payRollGeneratedMonths) {
                    if ($scope.payRollGeneratedMonths[i].MONTH_ID == $scope.monthId) {
                        $scope.viewMonthlySheet($scope.monthId);
                        generateBtn.text("Regenerate");
                        generateBtn.attr("regenerateFlag", true);
                        break;
                    }
                }

            };

            var headers = [];
            var datas = [];

            var initializeHeaders = function (cols) {
                headers = [];
                headers.push({field: "employeeName", title: "Employee Name"});
                for (var i in cols) {
                    headers.push({field: "h" + i, title: cols[i]});
                }
                headers.push({field: "calculatedValue", title: "Calculated Value"});
            };

            var initializeDatas = function (cols, rows) {
                datas = [];
                for (var i in rows) {
                    var temp = {};
                    temp.employeeName = $scope.employeeList[i];
                    for (var j in cols) {
                        temp["h" + j  ] = rows[i].ruleValueKV[j];
                    }
                    temp.calculatedValue = rows[i].calculatedValue;
                    datas.push(temp);
                }
            };

            var initializekendoGrid = function (columns, datas) {
                $("#salarySheetTable").kendoGrid({
                    toolbar: ["excel"],
                    excel: {
                        fileName: "SalarySheet.xlsx",
                        filterable: true,
                        allPages: true
                    },
                    dataSource: {
                        data: datas,
                        pageSize: 20,
                    },
                    height: 450,
                    scrollable: true,
                    sortable: true,
                    filterable: true,
                    pageable: {
                        input: true,
                        numeric: false
                    },
                    columns: columns
                });

            };

        });

