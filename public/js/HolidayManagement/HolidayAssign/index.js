(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller('HolidayAssignController', function ($scope) {
            var $companyId = angular.element(document.getElementById('companyId'));
            var $branchId = angular.element(document.getElementById('branchId'));
            var $departmentId = angular.element(document.getElementById('departmentId'));
            var $designationId = angular.element(document.getElementById('designationId'));
            var $positionId = angular.element(document.getElementById('positionId'));
            var $serviceTypeId = angular.element(document.getElementById('serviceTypeId'));
            var $serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId'));
            var $employeeId = angular.element(document.getElementById('employeeId'));



            $scope.employeeList = [];
            $scope.alreadyAssignedEmpList = [];
            $scope.all = false;
            $scope.assignShowHide = false;

            $scope.checkAll = function (item) {
                for (var i = 0; i < $scope.employeeList.length; i++) {
                    $scope.employeeList[i].checked = item;
                }
                $scope.assignShowHide = item && ($scope.employeeList.length > 0);
            };

            $scope.checkUnit = function (item) {
                for (var i = 0; i < $scope.employeeList.length; i++) {
                    if ($scope.employeeList[i].checked) {
                        $scope.assignShowHide = true;
                        break;
                    }
                    $scope.assignShowHide = false;
                }
            };

            $scope.holidayChangeFn = function () {
                $scope.all = false;
                $scope.assignShowHide = false;


                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.wsGetHolidayAssignedEmployees, {
                    holidayId: 1
                }).then(function (response) {
                    App.unblockUI("#hris-page-content");
                    console.log("shift Assign Filter Success Response", response);
                    if (response.success) {
                        $scope.$apply(function () {
                            $scope.alreadyAssignedEmpList = response.data;
                        });
                    } else {
                        console.log("getHolidayAssignedEmployees=>", response.error);
                    }

                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log("shift Assign Filter Failure Response", failure);
                });
            };

            $scope.view = function () {
                window.app.pullDataById(document.wsGetEmployeeList, {
                    companyId: $companyId.val(),
                    branchId: $branchId.val(),
                    departmentId: $departmentId.val(),
                    designationId: $designationId.val(),
                    positionId: $positionId.val(),
                    serviceTypeId: $serviceTypeId.val(),
                    serviceEventTypeId: $serviceEventTypeId.val(),
                    employeeId: $employeeId.val()
                }).then(function (response) {
                    $scope.$apply(function () {
                        var empList = response.data;
                        for (var i in empList) {
                            var emp = empList[i];
                            emp.checked = ($scope.alreadyAssignedEmpList.indexOf(emp.EMPLOYEE_ID) >= 0);
                            $scope.employeeList.push(emp);
                        }
                    });

                }, function (failure) {

                });
                $scope.holidayChangeFn();
            };

            $scope.assign = function () {
                var checkedEmpList = [];
                for (var index in $scope.employeeList) {
                    if ($scope.employeeList[index].checked) {
                        checkedEmpList.push($scope.employeeList[index].EMPLOYEE_ID);
                    }
                }
                window.app.pullDataById(document.wsAssignHolidayToEmployees, {
                    holidayId: 1,
                    employeeIdList: checkedEmpList
                }).then(function (response) {

                }, function (failure) {

                });

            };

        });