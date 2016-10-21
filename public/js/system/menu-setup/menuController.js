/**
 * Created by root on 10/19/16.
 */
var angularApp = angular.module('hris',['ui.bootstrap']);

var menuId="";
angularApp.controller('menuUpdateController',function($scope,$http){
    $scope.menuDtl = {
        menuCode:'',
        menuName:'',
        url:'',
        menuDescription:'',
        menuId:''
    };

    $( document ).on( 'click', '#tree_3 ul li a', function() {

        $("#addChild").attr("data-target","#draggable");
        $('#editForm').css('display','block');

        var attrId = $(this).attr("id");
        menuId =  attrId.split("_")[0];

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
});

angularApp.controller('menuInsertionController',function($scope,$http){
    $scope.menuDtl = {
        menuCode:'',
        menuName:'',
        url:'',
        menuDescription:''
    };
    var parentMenu = "";
    $("#addParent").on("click",function () {
        parentMenu=null;
    });
    $("#addChild").on("click",function () {
        parentMenu=menuId;
    });
    $scope.submitForm = function () {
        if ($scope.userForm.$valid) {
            window.app.pullDataById(document.url, {
                action: 'menuInsertion',
                data: {
                    dataArray: $scope.menuDtl,
                    parentMenu: parentMenu
                },
            }).then(function (success) {
                $scope.$apply(function () {
                    var newData = success.menuData;
                    $("#tree_3").jstree(true).settings.core.data = newData;
                    $("#tree_3").jstree(true).refresh();

                    $(".menusForm").modal("toggle");
                    window.toastr.success(success.data, "Notifications");
                });
            }, function (failure) {
                console.log(failure);
            });
       }
    }
});



// angularApp.controller('ModalDemoCtrl', function ($uibModal, $log, $document) {
//     var $ctrl = this;
//
//     $ctrl.open = function (size, parentSelector) {
//         var parentElem = parentSelector ?
//             angular.element($document[0].querySelector('.modal-demo ' + parentSelector)) : undefined;
//         var modalInstance = $uibModal.open({
//             animation: $ctrl.animationsEnabled,
//             ariaLabelledBy: 'modal-title',
//             ariaDescribedBy: 'modal-body',
//             templateUrl: 'myModalContent.html',
//             controller: 'ModalInstanceCtrl',
//             controllerAs: '$ctrl',
//             size: size,
//             appendTo: parentElem
//
//         });
//     };
// });
//
// angularApp.controller('ModalInstanceCtrl', function ($scope,$uibModalInstance) {
//     var $ctrl = this;
//
//     $ctrl.cancel = function () {
//         $uibModalInstance.dismiss('cancel');
//     };
//
//     $scope.menuDtl = {
//         menuCode:'',
//         menuName:'',
//         url:'',
//         menuDescription:''
//     };
//     var parentMenu = "";
//     $("#addParent").on("click",function () {
//         parentMenu=null;
//     });
//     $("#addChild").on("click",function () {
//         parentMenu=menuId;
//     });
//     $scope.submitForm = function () {
//         if ($scope.userForm.$valid) {
//             window.app.pullDataById(document.url, {
//                 action: 'menuInsertion',
//                 data: {
//                     dataArray: $scope.menuDtl,
//                     parentMenu: parentMenu
//                 },
//             }).then(function (success) {
//                 $scope.$apply(function () {
//                     var newData = success.menuData;
//                     $("#tree_3").jstree(true).settings.core.data = newData;
//                     $("#tree_3").jstree(true).refresh();
//
//                     $uibModalInstance.dismiss('cancel');
//                     window.toastr.success(success.data, "Notifications");
//                 });
//             }, function (failure) {
//                 console.log(failure);
//             });
//        }
//     }
//
// });

// Please note that the close and dismiss bindings are from $uibModalInstance.




