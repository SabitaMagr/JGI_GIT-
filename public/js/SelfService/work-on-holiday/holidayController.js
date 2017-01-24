angular.module('hris', [])
        .controller('holidayController', function ($scope, $http) {
        $scope.employeeChange = function () {
            var employeeId = angular.element(document.getElementById('employeeId')).val();
            console.log(employeeId);
            window.app.floatingProfile.setDataFromRemote(employeeId);
            window.app.pullDataById(document.url, {
            action: 'pullHolidaysForEmployee',
                    data: {
                    'employeeId': employeeId
                    }
            }).then(function (success) {
                $scope.$apply(function () {
                console.log(success.data);

                $scope.holidayList = success.data;
                $scope.holidayId = $scope.holidayList[0];
                });
            });
        }
});
