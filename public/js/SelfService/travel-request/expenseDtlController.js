angular.module('hris', [])
        .controller('expenseDtlController', function ($scope, $http) {
            $scope.expenseDtlFormList = [];
            $scope.counter = '';
            $scope.expenseDtlFormTemplate = {
                id: 0,
                departureDate: "",
                departureTime: "",
                departurePlace: "",
                destinationDate: "",
                destinationTime: "",
                destinationPlace: "",
                transportType: "",
                fare: "",
                allowance: "",
                localConveyence: "",
                miscExpenses: "",
                remarks: "",
                checkbox: "checkboxt0",
                checked: false
            };
            $scope.counter = 1;
            $scope.expenseDtlFormList.push(angular.copy($scope.expenseDtlFormTemplate));

            $scope.addExpenseDtl = function () {
                $scope.expenseDtlFormList.push(angular.copy({
                    id: 0,
                    departureDate: "",
                    departureTime: "",
                    departurePlace: "",
                    destinationDate: "",
                    destinationTime: "",
                    destinationPlace: "",
                    transportType: "",
                    fare: "",
                    allowance: "",
                    localConveyence: "",
                    miscExpenses: "",
                    remarks: "",
                    checkbox: "checkboxt" + $scope.counter,
                    checked: false
                }));
                $scope.counter++;
            };
            $scope.deleteExpenseDtl = function () {
                var tempT = 0;
                var lengthT = $scope.expenseDtlFormList.length;
                for (var i = 0; i < lengthT; i++) {
                    if ($scope.expenseDtlFormList[i - tempT].checked) {
                        var id = $scope.expenseDtlFormList[i - tempT].id;
                        $scope.expenseDtlFormList.splice(i - tempT, 1);
                        tempT++;
                    }
                }
            }
        });


