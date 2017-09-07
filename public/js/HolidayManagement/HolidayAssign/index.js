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
            var $genderId = angular.element(document.getElementById('genderId'));
            var $employeeTypeId = angular.element(document.getElementById('employeeTypeId'));


            $scope.holidayList = document.holidayList;

            $scope.employeeList = [];
            $scope.alreadyAssignedEmpList = [];
            $scope.all = false;

            $scope.checkAll = function (item) {
                for (var i = 0; i < $scope.employeeList.length; i++) {
                    $scope.employeeList[i].checked = item;
                }
            };


            $scope.holidayChangeFn = function () {
                if ($scope.holiday == null) {
                    $scope.alreadyAssignedEmpList = [];
                    var empList = angular.copy($scope.employeeList);
                    $scope.employeeList = [];
                    for (var i in empList) {
                        var emp = empList[i];
                        emp.checked = ($scope.alreadyAssignedEmpList.indexOf(emp.EMPLOYEE_ID) >= 0);
                        $scope.employeeList.push(emp);
                    }
                    return;
                }

                $scope.all = false;

                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.wsGetHolidayAssignedEmployees, {
                    holidayId: $scope.holiday
                }).then(function (response) {
                    App.unblockUI("#hris-page-content");
                    console.log("shift Assign Filter Success Response", response);
                    if (response.success) {
                        $scope.$apply(function () {
                            $scope.alreadyAssignedEmpList = response.data;
                            var empList = angular.copy($scope.employeeList);
                            $scope.employeeList = [];
                            for (var i in empList) {
                                var emp = empList[i];
                                emp.checked = ($scope.alreadyAssignedEmpList.indexOf(emp.EMPLOYEE_ID) >= 0);
                                $scope.employeeList.push(emp);
                            }
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
                    employeeId: $employeeId.val(),
                    genderId: $genderId.val(),
                    employeeTypeId: $employeeTypeId.val()
                }).then(function (response) {
                    $scope.$apply(function () {
                        $scope.employeeList = [];
                        var empList = response.data;
                        for (var i in empList) {
                            var emp = empList[i];
                            emp.checked = ($scope.alreadyAssignedEmpList.indexOf(emp.EMPLOYEE_ID) >= 0);
                            $scope.employeeList.push(emp);
                        }
                    });
                    window.app.scrollTo('employeeTable');

                }, function (failure) {

                });
                $scope.holidayChangeFn();
            };

            $scope.assign = function () {
                if ($scope.employeeList.length == 0) {
                    window.app.showMessage("No Employees to Assign.", "error");
                    return;
                }
                if ($scope.holiday == null) {
                    window.app.showMessage("Select the holiday first to assign to", "error");
                    return;
                }


                var checkedEmpList = [];
                for (var index in $scope.employeeList) {
                    if ($scope.employeeList[index].checked) {
                        checkedEmpList.push($scope.employeeList[index].EMPLOYEE_ID);
                    }
                }
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.wsAssignHolidayToEmployees, {
                    holidayId: $scope.holiday,
                    employeeIdList: checkedEmpList
                }).then(function (response) {
                    App.unblockUI("#hris-page-content");
                    if (response.success) {
                        window.app.showMessage("Holiday Assigned Successfully");
                    } else {
                        window.app.showMessage(response.error);
                    }
                }, function (failure) {
                    console.log("shift Assign Filter Success Response", failure);
                });

            };

        });