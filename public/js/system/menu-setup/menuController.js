/**
 * Created by root on 10/19/16.
 */
var angularApp = angular.module('hris',[]);

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

        $("#addChild").attr("href","#draggable");
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

    $scope.submit = function () {
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
    };
});

angularApp.controller('menuController',function($scope,$http){

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
    $scope.submit = function () {
        window.app.pullDataById(document.url, {
            action: 'menuInsertion',
            data: {
                dataArray: $scope.menuDtl,
                parentMenu:parentMenu
            },
        }).then(function (success) {
            $scope.$apply(function () {
                var newData = success.menuData;
                $("#tree_3").jstree(true).settings.core.data = newData;
                $("#tree_3").jstree(true).refresh();

                $('#draggable').modal('toggle');
                window.toastr.success(success.data, "Notifications");
            });
        }, function (failure) {
            console.log(failure);
        });
    };
});

