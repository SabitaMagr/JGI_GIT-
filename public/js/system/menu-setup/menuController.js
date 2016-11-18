/**
 * Created by root on 10/19/16.
 */
var angularApp = angular.module('hris', ['ui.bootstrap']);

angularApp.controller('menuUpdateController', function ($scope, $uibModal, $log, $document) {
    var menuId = "";
    $scope.menuDtl = {
        menuCode: '',
        menuName: '',
        route: '',
        action: '',
        menuIndex: '',
        iconClass: '',
        menuDescription: '',
        menuId: ''
    };

    $scope.permissionList = function (menuId) {
        $('#rolePanel').css('display', 'block');
        window.app.pullDataById(document.url, {
            action: 'pullRolePermissionList',
            data: {
                menuId: menuId
            },
        }).then(function (success) {
            $scope.$apply(function () {
                $scope.roleList = success.data;
                $scope.assignedList = success.data1;

                for (var i in $scope.roleList) {
                    for (var j in $scope.assignedList) {
                        if ($scope.roleList[i].ROLE_ID == $scope.assignedList[j].ROLE_ID) {
                            $scope.roleList[i].checked = true;
                            break;
                        }
                    }
                }
            });
        }, function (failure) {
            console.log(failure);
        });

    };
    $scope.assignRole = function (roleDtl) {
        var roleId = roleDtl.ROLE_ID;
        var checked = roleDtl.checked;
        var selectedMenu = menuId;

        window.app.pullDataById(document.url, {
            action: 'permissionAssign',
            data: {
                roleId: roleId,
                menuId: selectedMenu,
                checked: checked
            },
        }).then(function (success) {
            window.toastr.success(success.data, "Notifications");

        }, function (failure) {
            console.log(failure);
        });
    }
    $scope.isDisabled = true;
    $(document).on('click', '#tree_3 ul li a', function () {
        //$("a#addChild").attr("ng-click", "open(true)");
        $('#editForm').css('display', 'block');

        var attrId = $(this).attr("id");
        menuId = attrId.split("_")[0];

        $scope.$apply(function () {
            $scope.permissionList(menuId);
        });

        window.app.pullDataById(document.url, {
            action: 'pullMenuDetail',
            data: {
                id: menuId
            },
        }).then(function (success) {
            $scope.$apply(function () {
                var temp = success.data;
                $scope.menuDtl.menuId = temp.MENU_ID;
                $scope.menuDtl.menuCode = temp.MENU_CODE;
                $scope.menuDtl.menuName = temp.MENU_NAME;
                $scope.menuDtl.route = temp.ROUTE;
                $scope.menuDtl.action = temp.ACTION;
                $scope.menuDtl.menuIndex = parseInt(temp.MENU_INDEX);
                $scope.menuDtl.iconClass = temp.ICON_CLASS;
                $scope.menuDtl.menuDescription = temp.MENU_DESCRIPTION;
                $scope.isDisabled = false;
            });
        }, function (failure) {
            console.log(failure);
        });
    });

    $scope.submitForm = function () {
        if ($scope.userForm.$valid) {
            window.app.pullDataById(document.url, {
                action: 'menuUpdate',
                data: {
                    dataArray: $scope.menuDtl
                },
            }).then(function (success) {
                $scope.$apply(function () {
                    var newData = success.menuData;
                    $("#tree_3").jstree(true).settings.core.data = newData;
                    $("#tree_3").jstree(true).refresh();

                    if (success.data != "") {
                        window.toastr.success(success.data, "Notifications");
                    }
                    if (success.menuIndexErr != "") {
                        $scope.menuIndexErr = success.menuIndexErr;
                    } else {
                        $scope.menuIndexErr = null;
                    }
                });
            }, function (failure) {
                console.log(failure);
            });
        }
        ;
    }
    $scope.deleteMenu = function () {
        window.app.pullDataById(document.url, {
            action: 'menuDelete',
            data: {
                menuId: menuId
            },
        }).then(function (success) {
            $scope.$apply(function () {
                var newData = success.menuData;
                $('#editForm').css('display', 'none');
                $('#rolePanel').css('display', 'none');
                $scope.menuDtl.menuName = "";
                $scope.isDisabled = true;
                
                $("#tree_3").jstree(true).settings.core.data = newData;
                $("#tree_3").jstree(true).refresh();

                window.toastr.success(success.data, "Notifications");
            });
        }, function (failure) {
            console.log(failure);
        });
    }

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
                $scope.menuDtl = {
                    menuCode: '',
                    menuName: '',
                    route: '',
                    action: '',
                    menuIndex: '',
                    iconClass: '',
                    menuDescription: '',
                };
                $scope.submitForm = function () {
                    if ($scope.userForm.$valid) {
                        window.app.pullDataById(document.url, {
                            action: 'menuInsertion',
                            data: {
                                dataArray: $scope.menuDtl,
                                parentMenu: menuId
                            },
                        }).then(function (success) {
                            $scope.$apply(function () {
                                var newData = success.menuData;
                                $("#tree_3").jstree(true).settings.core.data = newData;
                                $("#tree_3").jstree(true).refresh();

                                // $uibModalInstance.dismiss('cancel');
                                if (success.data != "") {
                                    window.toastr.success(success.data, "Notifications");
                                    $uibModalInstance.close('cancel');
                                }
                                if (success.menuIndexErr != "") {
                                    $scope.menuIndexErr = success.menuIndexErr;
                                } else {
                                    $scope.menuIndexErr = null;
                                }
                            });
                        }, function (failure) {
                            console.log(failure);
                        });
                    }
                }
            },
            controllerAs: '$ctrl',
            resolve: {
                menuId: function () {
                    return type ? menuId : null;
                }
            }
        });

        modalInstance.result.then(function (selectedItem) {
            console.log("Model closed with following result", selectedItem);
        }, function () {
            console.log("Model Disposed");
        });
    };

//   END OF MODEL CODE

});






