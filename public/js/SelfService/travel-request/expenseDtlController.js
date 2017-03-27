angular.module('hris', [])
        .controller('expenseDtlController', function ($scope, $http) {
            $scope.expenseDtlFormList = [];
            $scope.counter = '';
            $scope.transportTypeList = [
                    {"id": "AP", "name": "Aero Plane"},
                    {"id": "OV", "name": "Office Vehicles"},
                    {"id": "TI", "name": "Taxi"},
                    {"id": "BS", "name": "Bus"},
                ];
            $scope.expenseDtlFormTemplate = {
                id: 0,
                departureDate: "",
                departureTime: "",
                departurePlace: "",
                destinationDate: "",
                destinationTime: "",
                destinationPlace: "",
                transportType: $scope.transportTypeList[0],
                fare: "",
                allowance: "",
                localConveyence: "",
                miscExpenses: "",
                total:"",
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
                    transportType:  $scope.transportTypeList[0],
                    fare: "",
                    allowance: "",
                    localConveyence: "",
                    miscExpenses: "",
                    total:"",
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
            $scope.submitExpenseDtl = function(){
                
            }
        });


