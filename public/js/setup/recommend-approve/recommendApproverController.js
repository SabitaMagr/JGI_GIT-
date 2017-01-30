angular.module('hris', [])
        .controller('recommedApproverController', function ($scope, $http) {
            var oldEmployeeId = angular.element(document.getElementById("oldEmployeeId")).val();
            window.app.floatingProfile.setDataFromRemote(oldEmployeeId);
            $scope.detail = {
                recommender: "",
                approver: ""
            };
            var getDetail = function () {
                window.app.pullDataById(document.url, {
                    action: 'pullEmpRecommendApproveDtl',
                    employeeId: oldEmployeeId
                }).then(function (success) {
                    $scope.$apply(function () {
                        if (success.data !== null) {
                            $scope.detail.recommender = success.data.RECOMMEND_BY;
                            $scope.detail.approver = success.data.APPROVED_BY;
                        }
                    });
                }, function (failure) {
                    console.log(failure);
                });

            };
            getDetail();
            var update = function () {
                window.app.floatingProfile.setDataFromRemote($scope.employeeId);
                window.app.pullDataById(document.url, {
                    action: 'pullRecommendApproveList',
                    employeeId: $scope.employeeId
                }).then(function (success) {
                    $scope.$apply(function () {

                        var oldRecommender = $scope.detail.recommender;
                        var oldApprover = $scope.detail.approver;

                        $scope.recommenderOptions = success.recommender;
                        $.each(success.recommender, function (key, value) {
                            if (value.id == oldRecommender) {
                                $scope.recommenderSelected = $scope.recommenderOptions[key];
                            } else {
                                $scope.recommenderSelected = $scope.recommenderOptions[0];
                            }
                        });

                        $scope.approverOptions = success.approver;
                        $.each(success.approver, function (key, value) {
                            if (value.id == oldApprover) {
                                $scope.approverSelected = $scope.approverOptions[key];
                            } else {
                                $scope.approverSelected = $scope.approverOptions[0];
                            }
                        });

                    });
                }, function (failure) {
                    console.log(failure);
                });
            };
            $scope.employeeId = oldEmployeeId;
            $scope.change = update;
            update();
        });
