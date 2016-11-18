/**
 * Created by punam on 10/5/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.addDatePicker(
            $("#fromDate"),
            $("#toDate")
        );
    });
})(window.jQuery, window.app);


angular.module('hris', [])
    .controller('attendanceController', function ($scope, $http) {
        $scope.attendanceList = [];
        $scope.view = function () {
            var employeeId = angular.element(document.getElementById('employeeId')).val();
            var fromDate = angular.element(document.getElementById('fromDate1')).val();
            var toDate = angular.element(document.getElementById('toDate1')).val();

            window.app.pullDataById(document.url, {
                action: 'pullAttendanceList',
                data: {
                    'fromDate':fromDate ,
                    'toDate':toDate,
                    'employeeId': employeeId
                }
            }).then(function (success) {
                $scope.$apply(function () {
                    $scope.attendanceList = success.data;
                });
            }, function (failure) {
                console.log(failure);
            });
        }

    });