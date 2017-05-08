(function ($, app) {
    'use strict';
    $(document).ready(function () {
        window.app.UIConfirmations();
        $('select').select2();
    });
})(window.jQuery, window.app);


angular.module('hris', [])
        .controller('assetIssuecontroller', function ($scope, $http, $window) {

            $scope.employeeList = [];
            $scope.all = false;
            $scope.retrunDate = true;

            $scope.returnableCheckbox = function (event) {
                console.log(event);
                if (event) {
                    $scope.retrunDate = false;
                } else {
                    $scope.retrunDate = true;
                }
            }
            
            
             var UpdateTotal = function () {
                var total = 0;
               
                for (var i = 0; i < $scope.employeeList.length; i++) {
                    if($scope.employeeList[i].checked){
                    var quantity = $scope.employeeList[i].QUANTITY;
                    total += quantity;
                }
                }
                $scope.assetTotalIssueQuantity=total;
                

            }


            $scope.issueBalfn = function(){
                UpdateTotal();
            }
            
            $scope.checkBoxChngfn = function(){
//                console.log('sdfdsf');
                UpdateTotal();
            }



            $scope.view = function () {
                $scope.assetTotalIssueQuantity=0;
                $scope.retrunDate = true;
                
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var assetId = angular.element(document.getElementById('assetId')).val();

//                console.log(assetId);

                if (assetId > 0) {
                    window.app.pullDataById(document.url, {
                        action: 'pullAssetBalance',
                        data: {
                            assetId: assetId
                        }
                    }).then(function (success) {
//                    console.log("Asset Balalnce", success.data);
                        $scope.assetAvailableStock = success.data;
                    }, function (failure) {
                        console.log("Asset Balalnce", failure);
                    });

                } else {
                    return;
                }


                $scope.all = false;
                $scope.assignShowHide = false;

                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullAssetIssueList',
                    data: {
                        branchId: branchId,
                        departmentId: departmentId,
                        designationId: designationId,
                        employeeId: employeeId,
                        positionId: positionId,
                        serviceTypeId: serviceTypeId,
                        assetId: assetId,
                        companyId: companyId,
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    console.log("Employee list for assign", success);
                    $scope.$apply(function () {
                        $scope.employeeList = success.data;
                        for (var i = 0; i < $scope.employeeList.length; i++) {
                            $scope.employeeList[i].checked = false;
                        }

                    });
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log("Employee Get All", failure);
                });




            }







        });