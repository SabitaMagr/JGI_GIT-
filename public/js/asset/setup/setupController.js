(function ($, app) {
    'use strict';


    $(document).ready(function () {

        app.addDatePicker($('#issueDate'));
        app.addDatePicker($('#requestDate'));
        app.addDatePicker($('#returnDate'));

    });

})(window.jQuery, window.app);


angular.module('hris', [])
        .controller('setupController', function ($scope, $http) {


            assetIssue = function (assetname,assetId) {
                console.log(assetname);
                console.log(assetId);
                $scope.asset=assetId;
                $scope.assetNameView=assetname;
                
                window.app.pullDataById(document.restfulUrl, {
                    action: 'pullAssetBalance',
                    data: {
                        assetId: $scope.asset
                    }

                }).then(function (success) {
                    $scope.$apply(function () {
                        $scope.rQ = 'REM BALANCE: ' + success.data;
                        $scope.bal = success.data;
                        $("#quantity").attr({"max": success.data, "min": 1});
                    });
                }, function (error) {
                    console.log("error", error);
                })
                
                
            }
            
            $scope.modalIssue = function(assetname,assetId){
                assetIssue(assetname,assetId);
            }
            


            $scope.astChange = function () {
                window.app.pullDataById(document.restfulUrl, {
                    action: 'pullAssetBalance',
                    data: {
                        assetId: $scope.asset
                    }

                }).then(function (success) {
                    $scope.$apply(function () {
                        $scope.rQ = 'REM BALANCE: ' + success.data;
                        $scope.bal = success.data;
                        $("#quantity").attr({"max": success.data, "min": 1});
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
