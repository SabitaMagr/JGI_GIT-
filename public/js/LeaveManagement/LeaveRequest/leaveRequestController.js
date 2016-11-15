/**
 * Created by punam on 9/30/16.
 */

angular.module('hris', [])
    .controller('leaveRequestController', function ($scope, $http) {

        var employeeId = angular.element(document.getElementById('employeeId')).val();
        var halfDay = angular.element(document.getElementById('halfDay'));

        window.app.floatingProfile.setDataFromRemote(document.employeeId);

        $scope.change = function() {
            var leaveId = angular.element(document.getElementById('leaveId')).val();
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

                    if($scope.allowHalfDay=='N'){
                        halfDay.slideUp();
                    }else{
                        halfDay.slideDown();
                    }

                });
            }, function (failure) {
                console.log(failure);
            });
        }

    });


$(function() {
    $('body').on('keydown', '#form-recommendedRemarks', function(e) {
        if (e.which === 32 &&  e.target.selectionStart === 0) {
            return false;
        }
    });
});

$(function() {
    $('body').on('keydown', '#form-approvedRemarks', function(e) {
        if (e.which === 32 &&  e.target.selectionStart === 0) {
            return false;
        }
    });
});