(function ($, app) {
    'use strict';
    $(document).ready(function () {
        window.app.UIConfirmations();
        $('select').select2();
    });
})(window.jQuery, window.app);


angular.module('hris', [])
        .controller('assetIssuecontroller', function ($scope, $http,$window) {
            
            $scope.employeeList = [];
            $scope.all = false;
            
            
            $scope.view= function(){
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                
                
                 $scope.all = false;
                $scope.assignShowHide = false;
                
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullEmployeeForTrainingAssign',
                    data: {
                        branchId: branchId,
                        departmentId: departmentId,
                        designationId: designationId,
                        employeeId: employeeId,
                        positionId: positionId,
                        serviceTypeId: serviceTypeId,
                        trainingId: -1,
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

            





        }).directive("datepicker", function () {
        return {
            restrict: "A",
            require: "ngModel",
            link: function (scope, elem, attrs, ngModelCtrl) {
//                $(elem).val(attrs.dvalue);
                app.addDatePicker($(elem));
            }
        }
    });