(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('.mt-multiselect').multiselect()
        $("select").select2();
    });
})(window.jQuery, window.app);

angular.module('hris', ["ui.multiselect"])
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

            $scope.view = function () {

                if ($scope.selectedMonthlyValues.length == 0) {
                    window.toastr.info("No Monthly Value selected!", "Notification");
                    return;
                }


                $scope.monthlyValuekeys = [];
                for (var i = 0; i < $scope.selectedMonthlyValues.length; i++) {
                    $scope.monthlyValuekeys.push($scope.selectedMonthlyValues[i].id);
                }
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

            $scope.selectAllFlag = false;
            $scope.selectedMonthlyValues = [];
            $scope.selectedMonthlyValuesChange = function () {
                if ($scope.selectedMonthlyValues.length == $scope.monthlyValues.length) {
                    $scope.selectAllFlag = true;
                } else {
                    $scope.selectAllFlag = false;
                }

            };

            $scope.selectAllFlagFN = function () {
                if ($scope.selectAllFlag) {
                    $scope.selectedMonthlyValues = [];
                    $scope.selectAllFlag = false;
                } else {
                    $scope.selectedMonthlyValues = $scope.monthlyValues;
                    $scope.selectAllFlag = true;
                }
            };


        });