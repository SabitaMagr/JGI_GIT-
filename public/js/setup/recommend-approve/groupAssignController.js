angular.module('hris', ['ui.bootstrap'])
        .controller('groupAssignController', function ($scope, $uibModal, $log, $document) {
            $('select').select2();
            var $tableContainer = $("#loadingDiv");
            $scope.employeeList = [];
            $scope.all = false;
            $scope.assignShowHide = false;
            $scope.showHideAssignBtn = false;
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
            $scope.checkReportingHierarchy = function () {
                if ($scope.recommenderAssign || $scope.approverAssign) {
                    $scope.showHideAssignBtn = true;
                } else {
                    $scope.showHideAssignBtn = false;
                }
            }
            $scope.view = function () {
                $scope.all = false;
                $scope.assignShowHide = false;
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                App.blockUI({target: "#hris-page-content"});
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
                    App.unblockUI("#hris-page-content");
                    console.log("Employee list for assign", success);
                    $scope.$apply(function () {
                        $scope.employeeList = success.data;
                        console.log(success.data);
                        for (var i = 0; i < $scope.employeeList.length; i++) {
                            $scope.employeeList[i].checked = false;
                        }

                    });
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log("Employee Get All", failure);
                });
            };
//            $scope.recommenderOptions = [];
//            $scope.approverOptions = [];

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
                            $scope.role = 'Recommender';
                        } else if (parseInt(type) == 3) {
                            $scope.role = 'Approver';
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
                        $scope.recommenderOptions = selectedItem;
                        $scope.recommenderSelected = $scope.recommenderOptions[0];
                        $scope.recommenderAssign = true;
                        $scope.showHideAssignBtn = true;
                    } else if (parseInt(type) === 3) {
                        $scope.approverOptions = selectedItem;
                        $scope.approverSelected = $scope.approverOptions[0];
                        $scope.approverAssign = true;
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
            if ($scope.recommenderAssign || $scope.approverAssign) {
                //l.stop();
            }
            $scope.assign = function () {
//                l.start();
//                l.setProgress(0.5);
                var recommenderElement = angular.element(document.getElementById('recommenderId'));
                var recommenderId = recommenderElement.val();
                var recommenderName = document.getElementById('recommenderId').options[document.getElementById('recommenderId').selectedIndex].text;
                var approverElement = angular.element(document.getElementById('approverId'));
                var approverId = approverElement.val();
                var approverName = document.getElementById('approverId').options[document.getElementById('approverId').selectedIndex].text;

                var errorFlagR = false;
                if ($scope.recommenderAssign) {
                    if (recommenderId == "?") {
//                        l.stop();
                        window.app.errorMessage(
                                "Recommender is required!!!",
                                "Application Error"
                                );
                        errorFlagR = true;
                    } else {
                        errorFlagR = false;
                    }
                } else {
//                    l.stop();
                }
                var errorFlagA = false;
                if ($scope.approverAssign) {
                    if (approverId == "?") {
//                        l.stop();
                        window.app.errorMessage(
                                "Approver is required!!!"
                                ,
                                "Application Error"
                                );
                        errorFlagA = true;
                    } else {
                        errorFlagA = false;
                    }
                } else {
//                    l.stop();
                }

                if (!errorFlagR && !errorFlagA) {
                    App.blockUI({target: "#hris-page-content"});
                    submitRecord(recommenderId, recommenderName, approverId, approverName);
                }
            };

            var submitRecord = function (recommenderId, recommenderName, approverId, approverName) {
                var promises = [];

                if (!$scope.recommenderAssign) {
                    var recommenderId1 = null;
                } else {
                    var recommenderId1 = recommenderId;
                }

                if (!$scope.approverAssign) {
                    var approverId1 = null;
                } else {
                    var approverId1 = approverId;
                }
                for (var index in $scope.employeeList) {
                    if ($scope.employeeList[index].checked) {
                        promises.push(window.app.pullDataById(document.url, {
                            action: 'assignEmployeeReportingHierarchy',
                            data: {
                                employeeId: $scope.employeeList[index].EMPLOYEE_ID,
                                recommenderId: recommenderId1,
                                approverId: approverId1
                            }
                        }));
                    }
                }
                Promise.all(promises).then(function (success) {
//                    l.stop();
                    App.unblockUI("#hris-page-content");
                    $scope.$apply(function () {
                        for (var index in $scope.employeeList) {
                            if ($scope.employeeList[index].checked) {
                                if ($scope.recommenderAssign) {
                                    if ($scope.employeeList[index].EMPLOYEE_ID == recommenderId) {
                                        var recommenderNameNew = null;
                                    } else {
                                        var recommenderNameNew = recommenderName;
                                    }
                                    $scope.employeeList[index].RECOMMENDER_NAME = recommenderNameNew;
                                }

                                if ($scope.approverAssign) {
                                    if ($scope.employeeList[index].EMPLOYEE_ID == approverId) {
                                        var approverNameNew = null;
                                    } else {
                                        var approverNameNew = approverName;
                                    }
                                    $scope.employeeList[index].APPROVER_NAME = approverNameNew;
                                }
                            }
                        }
                    });
                    window.toastr.success("Reporting Hierarchy assigned successfully!", "Notification");
                });
            };


        });

