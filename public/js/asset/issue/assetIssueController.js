angular.module('hris', [])
        .controller('assetIssuecontroller', function ($scope, $http) {

            $scope.rdChk = false;
//    $scope.rQ='sdf';
            $scope.astChange = function () {
                window.app.pullDataById(document.restfulUrl, {
                    action: 'pullAssetBalance',
                    data: {
                            assetId: $scope.employee
                        }

                }).then(function (success) {
                    $scope.$apply(function () {
                        $scope.rQ = 'REM BALANCE: '+success.data;
                        $scope.bal = success.data;
                    $("#quantity").attr({"max" : success.data,"min" : 1});
//                    $("#quantity").attr("max",success.data.QUANTITY);
                    });
                }, function (error) {
                    console.log("error", error);
                })
            };

            $scope.rdClk = function () {
                if ($scope.rdChk == false) {
                    $("#returnDate").prop('required', false);
                    $scope.rdTxt = '';
                } else {
                    $("#returnDate").prop('required', true);
                }
            }
        });