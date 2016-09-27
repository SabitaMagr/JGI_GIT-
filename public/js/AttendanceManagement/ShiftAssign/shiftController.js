angular.module('hris', [])
    .controller('shiftAssignController', function ($scope, $http) {
        $scope.employeeShiftList = [];
        $scope.all = false;
        $scope.assignShowHide = false;

        $scope.checkAll = function (item) {
            for (var i = 0; i < $scope.employeeShiftList.length; i++) {
                $scope.employeeShiftList[i].checked = item;
            }
            $scope.assignShowHide = item && ($scope.employeeShiftList.length > 0);
        };

        $scope.checkUnit = function (item) {
            for (var i = 0; i < $scope.employeeShiftList.length; i++) {
                if ($scope.employeeShiftList[i].checked) {
                    $scope.assignShowHide = true;
                    break;
                }
                $scope.assignShowHide = false;
            }

        };

        $scope.view = function () {
            $scope.all = false;
            $scope.assignShowHide = false;

            var branchId = angular.element(document.getElementById('branchId')).val();
            var departmentId = angular.element(document.getElementById('departmentId')).val();
            var designationId = angular.element(document.getElementById('designationId')).val();
            var positionId = angular.element(document.getElementById('positionId')).val();
            var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();

            window.app.pullDataById(document.url, {
                action: 'pullEmployeeForShiftAssign',
                id: {
                    branchId: branchId,
                    departmentId: departmentId,
                    designationId: designationId,
                    positionId: positionId,
                    serviceTypeId: serviceTypeId,
                }
            }).then(function (success) {
                console.log("shift Assign Filter Success Response", success);
                $scope.$apply(function () {
                    $scope.employeeShiftList = success.data;
                    for (var i = 0; i < $scope.employeeShiftList.length; i++) {
                        $scope.employeeShiftList[i].checked = false;
                    }

                });

            }, function (failure) {
                console.log("shift Assign Filter Failure Response", failure);
            });
        };

        $scope.assign = function () {
            var shiftId = angular.element(document.getElementById('shiftId')).val();
            var shiftName = document.getElementById('shiftId').options[document.getElementById('shiftId').selectedIndex].text;

            console.log(shiftName);
            var promises = [];
            for (var index in $scope.employeeShiftList) {
                console.log($scope.employeeShiftList[index]);
                if ($scope.employeeShiftList[index].checked) {
                    promises.push(window.app.pullDataById(document.url, {
                        action: 'assignEmployeeShift',
                        data: {
                            employeeId: $scope.employeeShiftList[index].EMPLOYEE_ID,
                            shiftId: shiftId,
                            oldShiftId:$scope.employeeShiftList[index].SHIFT_ID
                        }
                    }));
                }
            }
            Promise.all(promises).then(function (success) {
                console.log(success);
                $scope.$apply(function () {
                    for (var index in $scope.employeeShiftList) {
                        if ($scope.employeeShiftList[index].checked) {
                            $scope.employeeShiftList[index].SHIFT_ENAME = shiftName;
                            $scope.employeeShiftList[index].SHIFT_ID = shiftId;
                        }
                    }
                });
                window.toastr.info("Shift assigned successfully!","Notification");
                // window.app.notification("Shift assigned successfully!", {position: "top right", className: "success"});
            });
        };

    });
