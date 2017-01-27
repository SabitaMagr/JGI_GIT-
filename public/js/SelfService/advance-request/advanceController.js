angular.module('hris', [])
        .controller('advanceController', function ($scope, $http) {
        var advanceChange = function () {
                var advanceId = angular.element(document.getElementById('advanceId')).val();
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                
                console.log(advanceId);
                console.log(employeeId);
                window.app.pullDataById(document.url, {
                action: 'pullAdvanceDetailByEmpId',
                        data: {
                        'employeeId': employeeId,
                        'advanceId': advanceId
                        }
                }).then(function (success) {
                    $scope.$apply(function () {
                        console.log(success.data);
                        $scope.allowAmt = success.data.allowAmt;
                        $scope.allowTerms = success.data.allowTerms;
                    });
                });
            };
            $scope.advanceChange = advanceChange;
            advanceChange();
            
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



