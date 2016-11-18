angular.module('hris', [])
        .controller('dashboardController', function ($scope) {
            $scope.roles = document.roles;
            $scope.role = null;
            $scope.dashboardItems = [];
            for (var key in document.dashboardItems) {
                $scope.dashboardItems.push({
                    value: key,
                    roleType: 'A',
                    checked: false
                });
            }

            $scope.roleTypes = document.roleTypes;
            $scope.roleChange = function ($role) {
                if ($role == null) {
                    return;
                }
                window.app.pullDataById(document.restfulUrl, {
                    action: 'fetchRoleDashboards',
                    data: {
                        roleId: $role['ROLE_ID']
                    }
                }).then(function (success) {
                    console.log(success);
                    $scope.$apply(function () {
                        for (var dashboardIndex in $scope.dashboardItems) {
                            $scope.dashboardItems[dashboardIndex].checked = false;
                            $scope.dashboardItems[dashboardIndex].roleType = 'A';
                        }
                        var data = success.data;
                        for (var dashboard in data) {
                            for (var dashboardIndex in $scope.dashboardItems) {
                                if ($scope.dashboardItems[dashboardIndex].value == data[dashboard]['DASHBOARD']) {
                                    $scope.dashboardItems[dashboardIndex].checked = true;
                                    $scope.dashboardItems[dashboardIndex].roleType = data[dashboard].ROLE_TYPE;
                                }
                            }
                        }
                    });
                }, function (failure) {
                    console.log("failure", failure);
                });
            };
            $scope.assign = function (dashboard, checked) {
                console.log(checked);
                if ($scope.role == null) {
                    alert("cannot assign ");
                    return;
                }
                var roleType = $scope.dashboardItems.filter(function (item) {
                    return  item.value == dashboard;
                })[0].roleType;
                window.app.pullDataById(document.restfulUrl, {
                    action: 'assignDashboard',
                    data: {
                        roleId: $scope.role["ROLE_ID"],
                        dashboard: dashboard,
                        roleType: roleType,
                        status: checked
                    }
                }).then(function (success) {
                    console.log(success);
                    $scope.$apply(function () {

                    });
                }, function (failure) {
                    console.log("failure", failure);
                });
            };
            console.log($scope.roles);
            console.log($scope.dashboardItems);
            console.log($scope.roleTypes);
        });