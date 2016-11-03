/**
 * Created by root on 11/3/16.
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
    .controller('serviceController', function ($scope, $http) {
        $scope.serviceHistory = [];
        $scope.view = function () {
            var employeeId = angular.element(document.getElementById('employeeId')).val();
            var fromDate = angular.element(document.getElementById('fromDate')).val();
            var toDate = angular.element(document.getElementById('toDate')).val();

            window.app.pullDataById(document.url, {
                action: 'pullServiceHistory',
                data: {
                    'fromDate':fromDate ,
                    'toDate':toDate,
                    'employeeId': employeeId
                }
            }).then(function (success) {
                $scope.$apply(function () {
                    //console.log(success.data);
                    $scope.serviceHistory = success.data;
                });
            }, function (failure) {
                console.log(failure);
            });
        }
    });