angular.module('hris', [])
        .controller('departmenetController', function ($scope) {

            




            $scope.companyChange = function () {
                if (document.branches[$scope.company] != undefined) {
                    $scope.branchValue = document.branches[$scope.company];
                } else {
                    $scope.branchValue = '';
                }
            }

        });