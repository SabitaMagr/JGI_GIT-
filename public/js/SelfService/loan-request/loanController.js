angular.module('hris', [])
        .controller('loanController', function ($scope, $http) {
        $scope.employeeChange = function () {
            var employeeId = angular.element(document.getElementById('employeeId')).val();
            console.log(employeeId);
            window.app.floatingProfile.setDataFromRemote(employeeId);
            window.app.pullDataById(document.url, {
            action: 'pullLoanList',
                    data: {
                    'employeeId': employeeId
                    }
            }).then(function (success) {
                $scope.$apply(function () {
                console.log(success.data);

                $scope.loanList = success.data;
                $scope.loanId = $scope.loanList[0];
                });
            });
        }
});
