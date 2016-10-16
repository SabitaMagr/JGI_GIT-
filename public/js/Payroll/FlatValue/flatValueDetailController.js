angular.module('hris', [])
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
        $scope.selectAllflatValue = function (allflatValue) {
            for (var index in $scope.flatValues) {
                $scope.flatValues[index].selected = allflatValue;
            }
        };

        $scope.view = function () {
            var tempflatValueCheckedFlag = false;
            for (var index in $scope.flatValues) {
                if ($scope.flatValues[index].selected) {
                    tempflatValueCheckedFlag = true;
                }
            }

            if (!tempflatValueCheckedFlag) {
                window.toastr.info("No flat Value selected!", "Notification");
                return;
            }


            $scope.flatValuekeys = [];
            $scope.flatValues.filter(function (flatValue) {
                if (flatValue.selected) {
                    $scope.flatValuekeys.push(flatValue.id);
                }
            });
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

        $scope.updateSelectAll = function () {
            for (var index in $scope.flatValues) {
                if (!$scope.flatValues[index].selected) {
                    $scope.allflatValue = false;
                    break;
                } else {
                    $scope.allflatValue = true;
                }
            }
        };

    });