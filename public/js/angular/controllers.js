/**
 * Created by ukesh on 9/16/16.
 */
angular.module('hris', [])
    .controller('assignController', function ($scope, $http) {
        $scope.leaveList = [];
        $scope.all = false;
        $scope.daysForAll = 0;
        $scope.daysForAllFlag = false;

        $scope.checkAll = function (item) {
            for (var i = 0; i < $scope.leaveList.length; i++) {
                $scope.leaveList[i].checked = item;
            }

            $scope.daysForAllFlag = item && $scope.leaveList.length > 0;
        };

        $scope.daysForAllChange = function (days) {
            for (var i = 0; i < $scope.leaveList.length; i++) {
                if ($scope.leaveList[i].checked) {
                    $scope.leaveList[i].BALANCE = days;
                }
            }
        };

        $scope.checkUnit = function (item) {
            for (var i = 0; i < $scope.leaveList.length; i++) {
                if ($scope.leaveList[i].checked) {
                    $scope.daysForAllFlag = true;
                    break;
                }
                $scope.daysForAllFlag = false;
            }

        };

        $scope.assign = function () {

            var promises = [];
            for (var index in $scope.leaveList) {
                if ($scope.leaveList[index].checked) {
                    promises.push(window.app.pullDataById(document.url, {
                        action: 'pushEmployeeLeave',
                        data: {
                            leaveId: $scope.leaveList[index].LEAVE_ID,
                            employeeId: $scope.leaveList[index].EMPLOYEE_ID,
                            balance: $scope.leaveList[index].BALANCE,
                            leave: leaveId
                        }
                    }));
                }
            }
            Promise.all(promises).then(function (success) {
                console.log(success);
                window.app.notification("Leave assigned successfully!", {position: "top right", className: "success"});
            });
        };
        var leaveId;
        $scope.view = function () {
            $scope.daysForAllFlag = false;
            $scope.all = false;
            leaveId = angular.element(document.getElementById('leaveId')).val();
            var branchId = angular.element(document.getElementById('branchId')).val();
            var departmentId = angular.element(document.getElementById('departmentId')).val();
            var genderId = angular.element(document.getElementById('genderId')).val();
            var designationId = angular.element(document.getElementById('designationId')).val();
            console.log(leaveId);

            window.app.pullDataById(document.url, {
                action: 'pullEmployeeLeave',
                id: {
                    leaveId: leaveId,
                    branchId: branchId,
                    departmentId: departmentId,
                    genderId: genderId,
                    designationId: designationId
                }
            }).then(function (success) {
                $scope.$apply(function () {
                    $scope.leaveList = success.data;
                    for (var i = 0; i < $scope.leaveList.length; i++) {
                        $scope.leaveList[i].checked = false;
                    }
                });

            }, function (failure) {
                console.log(failure);

            });
        };
    });
