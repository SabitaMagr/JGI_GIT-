(function ($, app) {
    'use strict';


    $(document).ready(function () {


        app.addDatePicker($('#issueDate'));
        app.addDatePicker($('#requestDate'));
        app.addDatePicker($('#returnDate'));

        $("form").submit(function () {
            App.blockUI({target: "form"});
        });

    });

})(window.jQuery, window.app);


angular.module('hris', ['ui.bootstrap'])
        .controller('setupController', function ($scope, $http, $uibModal) {
            
            $scope.assetNameView = 'asdasadas';

//            $('select').select2();
            
            $scope.asset = '1';
                $scope.assetNameView = 'lenovo';

            $scope.assetIssue = function (assetname, assetId) {
//                console.log(assetname);
//                console.log(assetId);
//                $scope.asset = assetId;
                $scope.assetNameView = assetname;
                
                console.log($scope.asset);
                console.log($scope.assetNameView);
//
//                window.app.pullDataById(document.restfulUrl, {
//                    action: 'pullAssetBalance',
//                    data: {
//                        assetId: $scope.asset
//                    }
//
//                }).then(function (success) {
//                    $scope.$apply(function () {
//                        if (success.data == null || success.data == 0 || success.data == 'undefined') {
//                            $('#IssueSubmitBtn').attr('disabled', 'disabled');
//                        } else {
//                            $('#IssueSubmitBtn').attr('disabled', false);
//                        }
//                        $scope.rQ = 'REM BALANCE: ' + success.data;
//                        $scope.bal = success.data;
//                        $("#quantity").attr({"max": success.data, "min": 1});
//                    });
//                }, function (error) {
//                    console.log("error", error);
//                })


            }


            $scope.radioClik = function () {
                console.log('sdfdsf');
                console.log($scope.rdChk);
                if ($scope.rdChk == false) {
                    $("#returnDate").prop('required', false);
                    $scope.rdTxt = '';
                } else {
                    $("#returnDate").prop('required', true);
                }
            }

            $scope.rdClk = function () {
                $scope.radioClik();
            }

            $scope.openmod = function () {
                console.log('sdfdsf');
            }



            $("#assetSetupTable").on("click", "#btnIssue", function () {
//                $('#myModal').modal('show');
                $('#returnedDate').val('');
                var issueButton = $(this);
                var selectedassetId = issueButton.attr('data-assetid');
                var selectedassetName = issueButton.attr('data-asset');

                $('#requestDate').val('');
                $('#issueDate').val('');
                $('#quantity').val('');
                $('#purposeTA').val('');
                $('#remarks').val('');
                $("#returnDate").val('');


                $scope.$apply(function () {
                    $scope.assetIssue(selectedassetName, selectedassetId);
                });
            });



            $scope.astChange = function () {
                window.app.pullDataById(document.restfulUrl, {
                    action: 'pullAssetBalance',
                    data: {
                        assetId: $scope.asset
                    }

                }).then(function (success) {
                    $scope.$apply(function () {
                        $scope.rQ = 'REM BALANCE: ' + success.data;
                        $scope.bal = success.data;
                        $("#quantity").attr({"max": success.data, "min": 1});
                    });
                }, function (error) {
                    console.log("error", error);
                })
            };







            $ctrl = this;
            $ctrl.animationsEnabled = false;
            $scope.assetIssueBtn = function (assetName,assetId) {
                
//                console.log(assetName);
//                console.log(assetId);
                var modalInstance = $uibModal.open({
                    animation: $ctrl.animationsEnabled,
                    ariaLabelledBy: 'modal-title',
                    ariaDescribedBy: 'modal-body',
                    templateUrl: 'myModalContent.html',
                    controller: function ($scope, $uibModalInstance) {
                        
                    $scope.assetIssue(assetName, assetId);
//                            $scope.assetNameView = assetName;
                        
                        $scope.assetIssuecancel = function () {
                            $uibModalInstance.dismiss('cancel');
                        };
                    },
                     controllerAs: '$ctrl'
                });
                
                modalInstance.result.then(function (result) {
                    console.log(result);
                });
                

                modalInstance.rendered.then(function () {
                    $("select").select2();
                    app.addDatePicker($('#issueDate'));
                    app.addDatePicker($('#requestDate'));
                    app.addDatePicker($('#returnDate'));
                });


            };












        }
        ).directive('assetissue', function ($parse) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            var selector = attrs.selector;
            var fun = $parse(attrs.assetissue);
            element.on('click', selector, function (e) {
                // no need to create a jQuery object to get the attribute 
                var idx = e.target.getAttribute('data-index');
                fun(scope)(idx);
//            console.log(e);
            });

        }
    };
});


// var selector = attrs.selector;
//            var fun = $parse(attrs.clickChildren);
//            element.on('click', selector, function (e) {
//                // no need to create a jQuery object to get the attribute 
//                var idx = e.target.getAttribute('data-index');
//                fun(scope)(idx);
//            });
