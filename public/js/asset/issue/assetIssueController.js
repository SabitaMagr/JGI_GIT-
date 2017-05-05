angular.module('hris', [])
        .controller('assetIssuecontroller', function ($scope, $http) {

            var editVal = document.editVal;

            $scope.rdChk = false;

            if (typeof (editVal) != 'undefined') {
                console.log(editVal);
                var editAsset=editVal.assetId;
                var editQuantity=editVal.quantity;
                if (editVal.returnable == 'Y') {
                    $scope.rdChk = true;
                    $scope.rdTxt = editVal.returnDate;
                }
            $scope.assetList=document.assetList;
            console.log($scope.assetList[editAsset]);
            $scope.asset=$scope.assetList[editAsset];
            }else{
                var editQuantity=0;
                var editAsset='jpt';
            }


            $scope.astChange = function () {
                window.app.pullDataById(document.restfulUrl, {
                    action: 'pullAssetBalance',
                    data: {
                        assetId: $scope.asset
                    }

                }).then(function (success) {
                    $scope.$apply(function () {
                        if ($scope.asset == editAsset ) {
                            var sucessPlusEditval = success.data +editQuantity ;
                            $scope.rQ = 'REM BALANCE: ' + sucessPlusEditval;
                            $scope.bal = sucessPlusEditval;
                            $("#quantity").attr({"max": sucessPlusEditval, "min": 1});
                        } else {
                            $scope.rQ = 'REM BALANCE: ' + success.data;
                            $scope.bal = success.data;
                            $("#quantity").attr({"max": success.data, "min": 1});
                        }
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