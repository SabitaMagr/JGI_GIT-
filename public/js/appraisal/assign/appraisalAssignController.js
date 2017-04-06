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
            var $tableContainer = $("#loadingTable");
            $scope.view = function () {
                $scope.all = false;
                $scope.assignShowHide = false;
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var appraisalId = angular.element(document.getElementById('appraisalId')).val();
                console.log(appraisalId);
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullEmployeeWidAssignDetail',
                    data: {
                        branchId: branchId,
                        departmentId: departmentId,
                        designationId: designationId,
                        employeeId: employeeId,
                        appraisalId:appraisalId
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
            $scope.assign = function () {
                l.start();
                l.setProgress(0.5);
                var reviewerElement = angular.element(document.getElementById('reviewerId'));
                var reviewerId = reviewerElement.val();
                var reviewerName = document.getElementById('reviewerId').options[document.getElementById('reviewerId').selectedIndex].text;
                
                var appraiserElement = angular.element(document.getElementById('appraiserId'));
                var appraiserId = appraiserElement.val();
                var appraiserName = document.getElementById('appraiserId').options[document.getElementById('appraiserId').selectedIndex].text;
               
                var appraisalElement = angular.element(document.getElementById('appraisalId'));
                var appraisalId = appraisalElement.val();
                var appraisalName = document.getElementById('appraisalId').options[document.getElementById('appraisalId').selectedIndex].text;
                
                var errorFlagR = false;
                if ($scope.reviewerAssign) {
                    if (reviewerId == "?") {
                        l.stop();
                        window.app.errorMessage(
                                "Reviewer is required!!!",
                                "Application Error"
                                );
                        errorFlagR = true;
                    } else {
                        errorFlagR = false;
                    }
                } else {
                    l.stop();
                }
                var errorFlagA = false;
                if ($scope.appraiserAssign) {
                    if (appraiserId == "?") {
                        l.stop();
                        window.app.errorMessage(
                                "Appraiser is required!!!"
                                ,
                                "Application Error"
                                );
                        errorFlagA = true;
                    } else {
                        errorFlagA = false;
                    }
                } else {
                    l.stop();
                }

                if (!errorFlagR && !errorFlagA) {
                    submitRecord(reviewerId, reviewerName, appraiserId, appraiserName,appraisalId,appraisalName);
                }
            };
            var submitRecord = function (reviewerId, reviewerName, appraiserId, appraiserName,appraisalId,appraisalName) {
                var promises = [];

                if (!$scope.reviewerAssign) {
                    var reviewerId1 = null;
                } else {
                    var reviewerId1 = reviewerId;
                }

                if (!$scope.appraiserAssign) {
                    var appraiserId1 = null;
                } else {
                    var appraiserId1 = appraiserId;
                }
                for (var index in $scope.employeeList) {
                    if ($scope.employeeList[index].checked) {
                        promises.push(window.app.pullDataById(document.url, {
                            action: 'assignAppraisal',
                            data: {
                                employeeId: $scope.employeeList[index].EMPLOYEE_ID,
                                reviewerId: reviewerId1,
                                appraiserId: appraiserId1,
                                appraisalId: appraisalId
                            }
                        }));
                    }
                }
                Promise.all(promises).then(function (success) {
                    l.stop();
                    $scope.$apply(function () {
                        for (var index in $scope.employeeList) {
                            if ($scope.employeeList[index].checked) {
                                if ($scope.reviewerAssign) {
                                    if ($scope.employeeList[index].EMPLOYEE_ID == reviewerId) {
                                        var reviewerNameNew = null;
                                    } else {
                                        var reviewerNameNew = reviewerName;
                                    }
                                    $scope.employeeList[index].REVIEWER_NAME = reviewerNameNew;
                                    $scope.employeeList[index].APPRAISAL_EDESC = appraisalName;
                                }

                                if ($scope.appraiserAssign) {
                                    if ($scope.employeeList[index].EMPLOYEE_ID == appraiserId) {
                                        var appraiserNameNew = null;
                                    } else {
                                        var appraiserNameNew = appraiserName;
                                    }
                                    $scope.employeeList[index].APPRAISER_NAME = appraiserNameNew;
                                    $scope.employeeList[index].APPRAISAL_EDESC = appraisalName;
                                }
                                console.log(appraiserName);
                            }
                        }
                    });
                    window.toastr.success("Reporting Hierarchy for Appraisal Assigned Successfully!", "Notification");
                });
            };
        });
