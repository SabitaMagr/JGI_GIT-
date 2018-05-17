angular.module('hris', [])
        .controller('assignController', function ($scope) {
            $('select').select2();
            $scope.leaveList = [];
            $scope.all = false;
            $scope.daysForAll = 0;
            $scope.prevBalForAll = 0;
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
                        $scope.leaveList[i].TOTAL_DAYS = days;
                    }
                }
            };
            $scope.prevBalForAllChange = function (days) {
                for (var i = 0; i < $scope.leaveList.length; i++) {
                    if ($scope.leaveList[i].checked) {
                        $scope.leaveList[i].PREVIOUS_YEAR_BAL = days;
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
                        promises.push(window.app.pullDataById(document.pushEmployeeLeaveLink, {
                            leaveId: $scope.leaveList[index].LEAVE_ID,
                            employeeId: $scope.leaveList[index].EMPLOYEE_ID,
                            leave: leaveId,
                            balance: $scope.leaveList[index].TOTAL_DAYS,
                            previousYearBal: $scope.leaveList[index].PREVIOUS_YEAR_BAL,
                        }));
                    }
                }
                Promise.all(promises).then(function (success) {
                    $scope.$apply(function () {
                        $scope.view();
                    });
                    window.toastr.success("Leave assigned successfully", "Notifications");
                });
            };
            var leaveId;
            $scope.leaveName;
            $scope.view = function () {
                $scope.daysForAllFlag = false;
                $scope.all = false;
                $scope.leaveName = $('#leaveId>option:selected').text();
                var q = document.searchManager.getSearchValues();
                q['leaveId'] = $('#leaveId').val();
                window.app.serverRequest(document.pullEmployeeLeaveLink, q).then(function (success) {
                    $scope.$apply(function () {
                        $scope.leaveList = success.data;
                        for (var i = 0; i < $scope.leaveList.length; i++) {
                            $scope.leaveList[i].checked = false;
                        }
                    });

                }, function (failure) {
                    throw failure;
                });
            };
            var employeeIdFromParam = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
            if (parseInt(employeeIdFromParam) > 0) {
                angular.element(document.getElementById('employeeId')).val(employeeIdFromParam).change();
                $scope.view();
            }
        });