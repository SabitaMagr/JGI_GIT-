angular.module('hris', [])
        .controller('advanceController', function ($scope, $http) {
        $scope.employeeChange = function () {
            var employeeId = angular.element(document.getElementById('employeeId')).val();
            console.log(employeeId);
            window.app.floatingProfile.setDataFromRemote(employeeId);
            window.app.pullDataById(document.url, {
            action: 'pullAdvanceList',
                    data: {
                    'employeeId': employeeId
                    }
            }).then(function (success) {
                $scope.$apply(function () {
                console.log(success.data);

                $scope.advanceList = success.data;
                $scope.advanceId = $scope.advanceList[0];
                });
            });
        }
});



