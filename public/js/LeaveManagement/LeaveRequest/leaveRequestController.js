/**
 * Created by punam on 9/30/16.
 */

angular.module('hris', [])
        .controller('leaveRequestController', function ($scope, $http) {

            var employeeId1 = angular.element(document.getElementById('employeeId')).val();
            var halfDay = angular.element(document.getElementById('halfDay'));

            window.app.floatingProfile.setDataFromRemote(employeeId1);

            $scope.change = function () {
                var leaveId = angular.element(document.getElementById('leaveId')).val();
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                window.app.pullDataById(document.url, {
                    action: 'pullLeaveDetail',
                    data: {
                        'leaveId': leaveId,
                        'employeeId': employeeId
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        var temp = success.data;
                        $scope.availableDays = temp.BALANCE;
                        $scope.allowHalfDay = temp.ALLOW_HALFDAY;

                        if ($scope.allowHalfDay == 'N') {
                            halfDay.slideUp();
                        } else {
                            halfDay.slideDown();
                        }

                        var availableDays = parseInt(temp.BALANCE);
                        var newValue = parseInt($("#noOfDays").val());

                        if ((availableDays != "" && newValue != "") && newValue > availableDays) {
                            $("#errorMsg").html("* Applied days can't be more than available days");
                            $("#request").attr("disabled", "disabled");
                        } else if ((availableDays != "" && newValue != "") && (newValue <= availableDays)) {
                            $("#errorMsg").html("");
                            $("#request").removeAttr("disabled");
                        }

                    });
                }, function (failure) {
                    console.log(failure);
                });
            }

            $scope.employeeChange = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();

                window.app.floatingProfile.setDataFromRemote(employeeId);

                window.app.pullDataById(document.url, {
                    action: 'pullLeaveDetailWidEmployeeId',
                    data: {
                        'employeeId': employeeId
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        var temp = success.data;
                        $scope.leaveList = success.leaveList;
                        $scope.leaveId = $scope.leaveList[0];
                        $scope.availableDays = temp.BALANCE;
                        $scope.allowHalfDay = temp.ALLOW_HALFDAY;

                        if ($scope.allowHalfDay == 'N') {
                            halfDay.slideUp();
                        } else {
                            halfDay.slideDown();
                        }
                        var availableDays = parseInt(temp.BALANCE);
                        var newValue = parseInt($("#noOfDays").val());

                        if ((availableDays != "" && newValue != "") && newValue > availableDays) {
                            $("#errorMsg").html("* Applied days can't be more than available days");
                            $("#request").attr("disabled", "disabled");
                        } else if ((availableDays != "" && newValue != "") && (newValue <= availableDays)) {
                            $("#errorMsg").html("");
                            $("#request").removeAttr("disabled");
                        }
                    });
                }, function (failure) {
                    console.log(failure);
                });

            }

        });


$(function () {
    $('body').on('keydown', '#form-recommendedRemarks', function (e) {
        if (e.which === 32 && e.target.selectionStart === 0) {
            return false;
        }
    });
});

$(function () {
    $('body').on('keydown', '#form-approvedRemarks', function (e) {
        if (e.which === 32 && e.target.selectionStart === 0) {
            return false;
        }
    });
});