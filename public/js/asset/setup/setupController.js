(function ($, app) {
    'use strict';


    $(document).ready(function () {
        app.addDatePicker($('#issueDate'));
        app.addDatePicker($('#requestDate'));
        app.addDatePicker($('#returnDate'));
    });

})(window.jQuery, window.app);


angular.module('hris', ['ui.bootstrap'])
        .controller('setupController', function ($scope, $http, $uibModal) {

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

            $("#assetSetupTable").on("click", "#btnIssue", function (e) {
                var issueButton = $(this);
                var selectedassetId = issueButton.attr('data-assetid');
                var selectedassetName = issueButton.attr('data-asset');
                $scope.assetIssueBtn(selectedassetName, selectedassetId);
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
            
///MODAL STARTS FROM HERE
            $ctrl = this;
            $ctrl.animationsEnabled = false;
            $scope.assetIssueBtn = function (assetName, assetId) {
                var modalInstance = $uibModal.open({
                    animation: $ctrl.animationsEnabled,
                    ariaLabelledBy: 'modal-title',
                    ariaDescribedBy: 'modal-body',
                    templateUrl: 'myModalContent.html',
                    controller: function ($scope, $uibModalInstance) {
                        $scope.asset = assetId;
                        $scope.assetNameView = assetName;
                        
                        window.app.pullDataById(document.restfulUrl, {
                            action: 'pullAssetBalance',
                            data: {
                                assetId: $scope.asset
                            }

                        }).then(function (success) {
                            $scope.$apply(function () {
                                if (success.data == null || success.data == 0 || success.data == 'undefined') {
                                    $('#IssueSubmitBtn').attr('disabled', 'disabled');
                                } else {
                                    $('#IssueSubmitBtn').attr('disabled', false);
                                }
                                $scope.rQ = 'REMAINING BALANCE: ' + success.data;
                                $scope.bal = success.data;
                                $("#quantity").attr({"max": success.data, "min": 1});
                            });
                        }, function (error) {
                            console.log("error", error);
                        })


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
                    $("#assetIssue-form").submit(function () {
                        App.blockUI({target: "form"});
                    });
                });


            };

        });