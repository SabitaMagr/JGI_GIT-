(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller('trainingAssignController', function ($scope, $http, $window) {
            $scope.employeeList = [];
            $scope.all = false;
            $scope.assignShowHide = false;
            var l;
            var $tableContainer = $("#trainingAssignListTable");

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
                var trainingId = angular.element(document.getElementById('trainingId')).val();
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var employeeTypeId = angular.element(document.getElementById('employeeTypeId')).val();

                trainingId = (typeof trainingId === 'undefined' || trainingId === null || trainingId === '') ? -1 : trainingId;

                $scope.all = false;
                $scope.assignShowHide = false;

                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.pullEmployeeForTrainingAssignLink, {
                    branchId: branchId,
                    departmentId: departmentId,
                    designationId: designationId,
                    employeeId: employeeId,
                    positionId: positionId,
                    serviceTypeId: serviceTypeId,
                    trainingId: trainingId,
                    companyId: companyId,
                    employeeTypeId: employeeTypeId
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
            };

            $scope.assign = function () {
                var trainingId = angular.element(document.getElementById('trainingId')).val();
                if (typeof trainingId === 'undefined' || trainingId === null || trainingId == '' || trainingId == -1) {
                    window.toastr.error("No Training Selected.", "Alert");
                    return;
                }

                var promises = [];
                for (var index in $scope.employeeList) {
                    if ($scope.employeeList[index].checked) {
                        if (trainingId == $scope.employeeList[index].TRAINING_ID) {
                            console.log($scope.employeeList[index].EMPLOYEE_ID + 'is already assigned');
                            continue;
                        }
                        promises.push(window.app.pullDataById(document.assignEmployeeTrainingLink, {
                            employeeId: $scope.employeeList[index].EMPLOYEE_ID,
                            trainingId: trainingId,
                            oldTrainingId: $scope.employeeList[index].TRAINING_ID
                        }));
                    }
                }

                if (promises.length > 0) {
                    l.start();
                    l.setProgress(0.5);
                } else {
                    window.toastr.success("Already Assigned", "Notification");
                }

                Promise.all(promises).then(function (response) {
                    l.stop();
                    var assignedStatus = false;
                    for (var i in response) {
                        if (response[i].success) {
                            assignedStatus = true;
                            window.toastr.success(response[i].message, "Notification");
                        } else {
                            window.toastr.error(response[i].message, "Error");
                        }
                    }
                    if (assignedStatus) {
                        $scope.$apply(function () {
                            $scope.view();
                        });
                    }

                }, function (error) {
                    l.stop();
                    console.log(error);
                });
            };

            var employeeIdFromParam = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
            if (parseInt(employeeIdFromParam) > 0) {
                angular.element(document.getElementById('employeeId')).val(employeeIdFromParam).change();
                $scope.view();
            }

        });