angular.module('hris', [])
        .controller('groupAssignController', function ($scope, $http) {
            $('select').select2();
            $scope.employeeList = [];
            $scope.all = false;
            $scope.assignShowHide = false;
            var l;

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

            $scope.view = function () {
                $scope.all = false;
                $scope.assignShowHide = false;

                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var employeeId = angular.element(document.getElementById('employeeId')).val();

                window.app.pullDataById(document.url, {
                    action: 'pullEmployeeForRecomApproverAssign',
                    // pullEmployeeForShiftAssign
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

            // MODEL CODE
            $ctrl = this;
            $ctrl.animationsEnabled = false;

            $scope.open = function (type) {
                var modalInstance = $uibModal.open({
                    animation: $ctrl.animationsEnabled,
                    ariaLabelledBy: 'modal-title',
                    ariaDescribedBy: 'modal-body',
                    templateUrl: 'myModalContent.html',
                    controller: function ($scope, $uibModalInstance, menuId) {
                        $scope.cancel = function () {
                            $uibModalInstance.dismiss('cancel');
                        };
   
                        $scope.submitForm = function () {
                        }
                    },
                    controllerAs: '$ctrl'
                });

                modalInstance.result.then(function (selectedItem) {
                    console.log("Model closed with following result", selectedItem);
                }, function () {
                    console.log("Model Disposed");
                });
            };

//   END OF MODEL CODE
        });

//(function ($) {
//    'use strict';
//    $(document).ready(function () {
//        $("#")
//    });
//})(window.jQuery);
//    