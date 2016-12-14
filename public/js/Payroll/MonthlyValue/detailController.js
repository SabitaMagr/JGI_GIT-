(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
    });
})(window.jQuery, window.app);


angular.module('hris', [])
        .controller('monthlyValueDetailController', function ($scope, $http) {
            $scope.branches = document.branches;
            $scope.departments = document.departments;
            $scope.designations = document.designations;
            $scope.monthlyValuesI = document.monthlyValues;
            $scope.monthlyValues = [];
            for (var index in $scope.monthlyValuesI) {
                $scope.monthlyValues.push({id: index, text: $scope.monthlyValuesI[index], selected: false});
            }

            $scope.branch;
            $scope.department;
            $scope.designation;

            $scope.monthlyValuekeys = [];
            var tableData;
            $scope.tableDataCopy;
            $scope.selectAllMonthlyValue = function (allMonthlyValue) {
                for (var index in $scope.monthlyValues) {
                    $scope.monthlyValues[index].selected = allMonthlyValue;
                }
            };

            $scope.view = function () {
                var tempMonthlyValueCheckedFlag = false;
                for (var index in $scope.monthlyValues) {
                    if ($scope.monthlyValues[index].selected) {
                        tempMonthlyValueCheckedFlag = true;
                    }
                }

                if (!tempMonthlyValueCheckedFlag) {
                    window.toastr.info("No Monthly Value selected!", "Notification");
                    return;
                }


                $scope.monthlyValuekeys = [];
                $scope.monthlyValues.filter(function (monthlyValue) {
                    if (monthlyValue.selected) {
                        $scope.monthlyValuekeys.push(monthlyValue.id);
                    }
                });
                window.app.pullDataById(document.url, {
                    action: 'pullEmployeeMonthlyValue',
                    id: {
                        branch: (($scope.branch === null) || (typeof $scope.branch === 'undefined')) ? -1 : $scope.branch,
                        department: (($scope.department === null) || (typeof $scope.department === 'undefined')) ? -1 : $scope.department,
                        designation: (($scope.designation === null) || (typeof $scope.designation === 'undefined')) ? -1 : $scope.designation,
                        monthlyValues: $scope.monthlyValuekeys
                    }
                }).then(function (success) {
                    console.log(success);
                    $scope.$apply(function () {
                        tableData = angular.copy(success.data);
                        $scope.tableDataCopy = success.data;
                    });

                }, function (failure) {
                    console.log("failure", failure);

                });
            };

            $scope.setMonthlyValue = function () {
                var promises = [];
                for (key in $scope.monthlyValuekeys) {
                    var loopKey = $scope.monthlyValuekeys[key];
                    for (var tableColData in tableData) {
                        if (tableData[tableColData][loopKey] != $scope.tableDataCopy[tableColData][loopKey]) {
                            console.log($scope.tableDataCopy[tableColData]);
                            promises.push(window.app.pullDataById(document.url, {
                                action: 'pushEmployeeMonthlyValue',
                                id: {
                                    employeeId: $scope.tableDataCopy[tableColData]["EMPLOYEE_ID"],
                                    mthId: loopKey,
                                    value: $scope.tableDataCopy[tableColData][loopKey]
                                }
                            }));

                        }
                    }
                }
                Promise.all(promises)
                        .then(function (success) {
                            console.log(success);
                            $scope.$apply(function () {
                                // tableData = angular.copy(success.data);
                                // $scope.tableDataCopy = success.data;
                            });
                            window.toastr.info("Monthly value assigned successfully!", "Notification");

                        }, function (failure) {
                            console.log("failure", failure);

                        });
            };

            $scope.changeAllColVal = function (keys, colVal) {
                console.log(keys);
                console.log(colVal);
                for (var emp in $scope.tableDataCopy) {
                    $scope.tableDataCopy[emp][keys] = colVal;
                    // console.log( $scope.tableDataCopy[emp][keys]);
                }
            };

            $scope.updateSelectAll = function () {
                for (var index in $scope.monthlyValues) {
                    if (!$scope.monthlyValues[index].selected) {
                        $scope.allMonthlyValue = false;
                        break;
                    } else {
                        $scope.allMonthlyValue = true;
                    }
                }
            };

        });