/**
 * Created by root on 11/9/16.
 */

angular.module('hris', [])
    .controller('holidayListController', function ($scope, $http) {
        $scope.holidayList = [];
        $scope.view = function () {
            var startDate = angular.element($("#startDate")).val();
            var endDate = angular.element($("#endDate")).val();
            var branchId = angular.element($("#branchId")).val();
            var genderId = angular.element($("#genderId")).val();

            window.app.pullDataById(document.url, {
                action: 'pullHolidayList',
                data: {
                    'fromDate': startDate,
                    'toDate': endDate,
                    'branchId': branchId,
                    'genderId': genderId
                }
            }).then(function (success) {
                $scope.$apply(function () {
                    $scope.holidayList = success.data;
                });
            }, function (failure) {
                console.log(failure);
            });
        }
    });