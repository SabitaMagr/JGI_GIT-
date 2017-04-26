(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('.mt-multiselect').multiselect();
        $('#branch').select2();
        $('#company').select2();
        $('#employee').select2();
        $('#department').select2();
        $('#designation').select2();
    });
})(window.jQuery, window.app);

angular.module('hris', ["ui.multiselect"])
        .controller('flatValueDetailController', function ($scope, $http) {
            $scope.branches = document.branches;
            $scope.departments = document.departments;
            $scope.designations = document.designations;
            $scope.flatValuesI = document.flatValues;
            $scope.flatValues = [];
            for (var index in $scope.flatValuesI) {
                $scope.flatValues.push({id: index, text: $scope.flatValuesI[index], selected: false});
            }

            $scope.branch;
            $scope.department;
            $scope.designation;

            $scope.flatValuekeys = [];
            var tableData;
            $scope.tableDataCopy;

            comBranchDeptDesignSearch("company","branch","department","designation","employee")
            $scope.view = function () {
                if ($scope.selectedFlatValues.length == 0) {
                    window.toastr.info("No flat Value selected!", "Notification");
                    return;
                }


                $scope.flatValuekeys = [];
                for (var i = 0; i < $scope.selectedFlatValues.length; i++) {
                    $scope.flatValuekeys.push($scope.selectedFlatValues[i].id);
                }
                var company = angular.element(document.getElementById('company')).val();
                var branch = angular.element(document.getElementById('branch')).val();
                var department = angular.element(document.getElementById('department')).val();
                var designation = angular.element(document.getElementById('designation')).val();
                var employee = angular.element(document.getElementById('employee')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullEmployeeFlatValue',
                    id: {
                        branch: ((branch === null) || (typeof branch === 'undefined')) ? -1 : branch,
                        department: ((department === null) || (typeof department === 'undefined')) ? -1 : department,
                        designation: ((designation === null) || (typeof designation === 'undefined')) ? -1 : designation,
                        company: ((company === null) || (typeof company === 'undefined')) ? -1 : company,
                        employee: ((employee === null) || (typeof employee === 'undefined')) ? -1 : employee,
                        flatValues: $scope.flatValuekeys
                    }
                }).then(function (success) {
                    console.log(success);
                    App.unblockUI("#hris-page-content");
                    $scope.$apply(function () {
                        tableData = angular.copy(success.data);
                        $scope.tableDataCopy = success.data;
                    });

                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log("failure", failure);

                });
            };

            $scope.setflatValue = function () {
                var promises = [];
                for (key in $scope.flatValuekeys) {
                    var loopKey = $scope.flatValuekeys[key];
                    for (var tableColData in tableData) {
                        if (tableData[tableColData][loopKey] != $scope.tableDataCopy[tableColData][loopKey]) {
                            console.log($scope.tableDataCopy[tableColData]);
                            promises.push(window.app.pullDataById(document.url, {
                                action: 'pushEmployeeFlatValue',
                                id: {
                                    employeeId: $scope.tableDataCopy[tableColData]["EMPLOYEE_ID"],
                                    flatId: loopKey,
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
                            window.toastr.info("flat value assigned successfully!", "Notification");

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
            $scope.selectedFlatValues = [];
            $scope.selectedFlatValuesChange = function () {
                if ($scope.selectedFlatValues.length == $scope.flatValues.length) {
                    $scope.selectAllFlag = true;
                } else {
                    $scope.selectAllFlag = false;
                }

            };

            $scope.selectAllFlagFN = function () {
                if ($scope.selectAllFlag) {
                    $scope.selectedFlatValues = [];
                    $scope.selectAllFlag = false;
                } else {
                    $scope.selectedFlatValues = $scope.flatValues;
                    $scope.selectAllFlag = true;
                }
            };

        });