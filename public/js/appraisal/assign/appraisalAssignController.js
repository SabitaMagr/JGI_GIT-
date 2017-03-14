(function ($, app) {
    'use strict';
    $(document).ready(function () {
        window.app.UIConfirmations();
        $('select').select2();
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller('appraisalAssignController', function ($scope, $http) {
            $scope.employeeList = [];
            $scope.all = false;
            $scope.assignShowHide = false;
            $scope.showHideAssignBtn = false;
            
            $scope.view = function () {
                $scope.all = false;
                $scope.assignShowHide = false;
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                window.app.pullDataById(document.url, {
                    action: 'pullEmployeeWidAssignDetail',
                    data: {
                        branchId: branchId,
                        departmentId: departmentId,
                        designationId: designationId,
                        employeeId: employeeId
                    }
                }).then(function (success) {
                    console.log("Employee list for assign", success);
                    $scope.$apply(function () {
                        $scope.employeeList = success.data;
                        console.log(success.data);
                        for (var i = 0; i < $scope.employeeList.length; i++) {
                            $scope.employeeList[i].checked = false;
                        }
                    });
                }, function (failure) {
                    console.log("Employee Get All", failure);
                });
            };
            var l = null;
            $scope.checkAll = function (item) {
                for (var i = 0; i < $scope.employeeList.length; i++) {
                    $scope.employeeList[i].checked = item;
                }
                $scope.assignShowHide = item && ($scope.employeeList.length > 0);
                if ($scope.assignShowHide) {
                    l = Ladda.create(document.querySelector('#assignBtn'));
                }
            };
            $scope.checkUnit = function (item) {
                for (var i = 0; i < $scope.employeeList.length; i++) {
                    if ($scope.employeeList[i].checked) {
                        $scope.assignShowHide = true;
                        l = Ladda.create(document.querySelector('#assignBtn'));
                        break;
                    }
                    $scope.assignShowHide = false;
                }
            };
        });
