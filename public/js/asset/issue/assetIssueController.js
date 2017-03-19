angular.module('hris', [])
        .controller('assetIssuecontroller', function ($scope, $http) {
            $scope.rdChk = false;
            $scope.astChange = function () {
                window.app.pullDataById(document.restfulUrl, {
                    action: 'pullMenuDetail',
                    data: {id: $scope.employee}

                }).then(function (success) {
                    console.log("success", success);
                }, function (error) {
                    console.log("error", error);
                })
            };



        });