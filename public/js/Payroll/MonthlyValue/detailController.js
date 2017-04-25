(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('.mt-multiselect').multiselect();
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

            $scope.showTable = false;
            comBranchDeptDesignSearch("company","branch","department","designation","employee")
            $scope.view = function () {
                if ($scope.selectedMonthlyValues.length === 0) {
                    window.toastr.info("No Monthly Values selected!", "Notification");
                    return;
                }


                $scope.monthlyValuekeys = [];
                for (var i = 0; i < $scope.selectedMonthlyValues.length; i++) {
                    $scope.monthlyValuekeys.push($scope.selectedMonthlyValues[i].id);
                }
                var company = angular.element(document.getElementById('company')).val();
                var branch = angular.element(document.getElementById('branch')).val();
                var department = angular.element(document.getElementById('department')).val();
                var designation = angular.element(document.getElementById('designation')).val();
                var employee = angular.element(document.getElementById('employee')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullEmployeeMonthlyValue',
                    id: {
                        branch: ((branch === null) || (typeof branch === 'undefined')) ? -1 : branch,
                        department: ((department === null) || (typeof department === 'undefined')) ? -1 : department,
                        designation: ((designation === null) || (typeof designation === 'undefined')) ? -1 : designation,
                        company: ((company === null) || (typeof company === 'undefined')) ? -1 : company,
                        employee: ((employee === null) || (typeof employee === 'undefined')) ? -1 : employee,
                        monthlyValues: $scope.monthlyValuekeys
                    }
                }).then(function (success) {
                    console.log(success);
                    App.unblockUI("#hris-page-content");
                    $scope.$apply(function () {
                        tableData = angular.copy(success.data);
                        $scope.tableDataCopy = success.data;
                        $scope.showTable = true;
                    });

                }, function (failure) {
                    App.unblockUI("#hris-page-content");
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
                            window.toastr.info("Monthly values assigned successfully!", "Notification");

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