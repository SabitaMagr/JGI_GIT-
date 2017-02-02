(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('.mt-multiselect').multiselect();
        $('#branch').select2();
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


            $scope.view = function () {
                if ($scope.selectedFlatValues.length == 0) {
                    window.toastr.info("No flat Value selected!", "Notification");
                    return;
                }


                $scope.flatValuekeys = [];
                for (var i = 0; i < $scope.selectedFlatValues.length; i++) {
                    $scope.flatValuekeys.push($scope.selectedFlatValues[i].id);
                }

                window.app.pullDataById(document.url, {
                    action: 'pullEmployeeFlatValue',
                    id: {
                        branch: (($scope.branch === null) || (typeof $scope.branch === 'undefined')) ? -1 : $scope.branch,
                        department: (($scope.department === null) || (typeof $scope.department === 'undefined')) ? -1 : $scope.department,
                        designation: (($scope.designation === null) || (typeof $scope.designation === 'undefined')) ? -1 : $scope.designation,
                        flatValues: $scope.flatValuekeys
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