var angularApp = angular.module('hris', ['ui.bootstrap']);

angularApp.controller('menuUpdateController', function ($scope, $uibModal, $log, $document) {
    var menuId = "";
    $scope.menuDtl = {
        menuCode: '',
        menuName: '',
        url: '',
        menuDescription: '',
        menuId: ''
    };

    $(document).on('click', '#tree_3 ul li a', function () {
        $("#addChild").attr("data-target", "#draggable");
        $('#editForm').css('display', 'block');

        var attrId = $(this).attr("id");
        menuId = attrId.split("_")[0];

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
                $scope.menuDtl.url = temp.URL;
                $scope.menuDtl.menuDescription = temp.MENU_DESCRIPTION;
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

                    window.toastr.success(success.data, "Notifications");
                });
            }, function (failure) {
                console.log(failure);
            });
        }
        ;
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
                    url: '',
                    menuDescription: ''
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
                                $uibModalInstance.close('cancel');

                                window.toastr.success(success.data, "Notifications");
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







