(function ($, app) {
    'use strict';
    $(document).ready(function () {
        window.app.UIConfirmations();
        $('select').select2();
    });
})(window.jQuery, window.app);

angular.module('hris', ['ui.bootstrap'])
        .controller('appraisalAssignController', function ($scope, $uibModal, $log, $document) {
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
            
            // MODEL CODE
            $ctrl = this;
            $ctrl.animationsEnabled = false;
            $scope.open = function (type) {
                console.log(type);
                var modalInstance = $uibModal.open({
                    animation: $ctrl.animationsEnabled,
                    ariaLabelledBy: 'modal-title',
                    ariaDescribedBy: 'modal-body',
                    templateUrl: 'myModalContent.html',
                    controller: function ($scope, $uibModalInstance) {
                        if (parseInt(type) == 2) {
                            $scope.role = 'Reviewer';
                        } else if (parseInt(type) == 3) {
                            $scope.role = 'Appraiser';
                        }
                        $scope.cancel = function () {
                            $uibModalInstance.dismiss('cancel');
                        };
                        $scope.filterForRole = function () {
                            var branchId = angular.element(document.getElementById('branchId')).val();
                            var departmentId = angular.element(document.getElementById('departmentId')).val();
                            var designationId = angular.element(document.getElementById('designationId')).val();
                            window.app.pullDataById(document.url, {
                                action: 'pullEmployeeListForReportingRole',
                                data: {
                                    branchId: branchId,
                                    departmentId: departmentId,
                                    designationId: designationId
                                }
                            }).then(function (success) {
                                console.log("Employee list for success", success.data);
                                $scope.$apply(function () {
                                    $uibModalInstance.close(success.data);
                                });
                            }, function (failure) {
                                console.log("Employee list for failure", failure);
                            });
                        }
                    },
                    controllerAs: '$ctrl'
                });
                modalInstance.result.then(function (selectedItem) {
                    if (parseInt(type) === 2) {
                        $scope.reviewerOptions = selectedItem;
                        $scope.reviewerSelected = $scope.reviewerOptions[0];
                        $scope.reviewerAssign = true;
                        $scope.showHideAssignBtn = true;
                    } else if (parseInt(type) === 3) {
                        $scope.appraiserOptions = selectedItem;
                        $scope.appraiserSelected = $scope.appraiserOptions[0];
                        $scope.appraiserAssign = true;
                        $scope.showHideAssignBtn = true;
                    }
                    console.log("Model closed with following result", selectedItem);
                }, function () {
                    console.log("Model Disposed");
                });
                modalInstance.rendered.then(function () {
                    $("select").select2();
                });
            };
            $scope.checkReportingHierarchy = function () {
                if ($scope.reviewerAssign || $scope.appraiserAssign) {
                    $scope.showHideAssignBtn = true;
                } else {
                    $scope.showHideAssignBtn = false;
                }
            }
        });
