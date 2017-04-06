angular.module('hris', ['ui.bootstrap'])
        .controller('assignController', function ($scope, $uibModal) {
            $('select').select2();
            var $tableContainer = $("#loadingDiv");
            $scope.leaveList = [];
            $scope.all = false;
            $scope.daysForAll = 0;
            $scope.daysForAllFlag = false;

            $scope.checkAll = function (item) {
                for (var i = 0; i < $scope.leaveList.length; i++) {
                    $scope.leaveList[i].checked = item;
                }

                $scope.daysForAllFlag = item && $scope.leaveList.length > 0;
            };

            $scope.daysForAllChange = function (days) {
                for (var i = 0; i < $scope.leaveList.length; i++) {
                    if ($scope.leaveList[i].checked) {
                        $scope.leaveList[i].BALANCE = days;
                    }
                }
            };

            $scope.checkUnit = function (item) {
                for (var i = 0; i < $scope.leaveList.length; i++) {
                    if ($scope.leaveList[i].checked) {
                        $scope.daysForAllFlag = true;
                        break;
                    }
                    $scope.daysForAllFlag = false;
                }
            };
            $scope.assign = function () {
                App.blockUI({target: "#hris-page-content"});
                var promises = [];
                for (var index in $scope.leaveList) {
                    if ($scope.leaveList[index].checked) {
                        promises.push(window.app.pullDataById(document.url, {
                            action: 'pushEmployeeLeave',
                            data: {
                                leaveId: $scope.leaveList[index].LEAVE_ID,
                                employeeId: $scope.leaveList[index].EMPLOYEE_ID,
                                balance: $scope.leaveList[index].BALANCE,
                                leave: leaveId
                            }
                        }));
                    }
                }
                Promise.all(promises).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    console.log(success);
                    window.toastr.success("Leave assigned successfully", "Notifications");
                });
            };
            var leaveId;
            $scope.leaveName;
            $scope.view = function () {
                $scope.daysForAllFlag = false;
                $scope.all = false;
                leaveId = angular.element(document.getElementById('leaveId')).val();
                var leaveList = document.querySelector('#leaveId');
                $scope.leaveName = leaveList.options[leaveList.selectedIndex].text;
                console.log($scope.leaveName);
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var genderId = angular.element(document.getElementById('genderId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                console.log(serviceTypeId);
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullEmployeeLeave',
                    id: {
                        leaveId: leaveId,
                        branchId: branchId,
                        departmentId: departmentId,
                        genderId: genderId,
                        designationId: designationId,
                        serviceTypeId: serviceTypeId
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    $scope.$apply(function () {
                        $scope.leaveList = success.data;
                        for (var i = 0; i < $scope.leaveList.length; i++) {
                            $scope.leaveList[i].checked = false;
                        }
                    });

                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);

                });
            };

            $scope.importExcel = function () {
                var modalInstance = $uibModal.open({
                    ariaLabelledBy: 'modal-title',
                    ariaDescribedBy: 'modal-body',
                    templateUrl: 'myModalContent.html',
                    resolve: {
                        fileTypes: function () {
                            return {};
                        }
                    },
                    controller: function ($scope, $uibModalInstance, fileTypes) {

                        $scope.valid = true;
                        $scope.ok = function () {
                            if (document.myDropzone.files.length == 0) {
                                $scope.valid = false;
                                return;
                            }
                            document.myDropzone.processQueue();
                        };
                        $scope.cancel = function () {
                            $uibModalInstance.dismiss('cancel');
                        };
                    }
                });
                modalInstance.rendered.then(function () {
                    document.myDropzone = new Dropzone("#dropZoneContainer", {
                        url: document.excelUploadUrl,
                        autoProcessQueue: false,
                        maxFiles: 1,
                        addRemoveLinks: true
                    });
                    document.myDropzone.on("success", function (file, success) {
                        console.log("File Upload Response", success);
                        modalInstance.close({test: "test"});
//                        $scope.$apply(function () {
//
//                        });
                    });
                });
                modalInstance.result.then(function (response) {
                    console.log("Angular Modal close response", response);
                }, function () {
                    console.log("Modal Action Cancelled");
                });


            };
        });