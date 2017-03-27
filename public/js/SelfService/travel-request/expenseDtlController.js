angular.module('hris', [])
        .controller('expenseDtlController', function ($scope, $http,$window) {
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
            var travelId = parseInt(angular.element(document.getElementById('travelId')).val());
            
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
                if ($scope.travelExpenseForm.$valid && $scope.expenseDtlFormList.length > 0) {
                    $scope.expenseDtlEmpty = 1;
                    if ($scope.expenseDtlFormList.length == 1 && angular.equals($scope.expenseDtlFormTemplate, $scope.expenseDtlFormList[0])) {
                        console.log("app log", "The form is not filled");
                        $scope.expenseDtlEmpty = 0;
                    }
                    console.log($scope.expenseDtlFormList);
                    window.app.pullDataById(document.urlExpenseRequest, {
                        data: {
                            expenseDtlList: $scope.expenseDtlFormList,
                            travelId: parseInt(travelId),
                            departureDate: $scope.departureDateMain,
                            returnedDate:$scope.returnedDate,
                            expenseDtlEmpty: parseInt($scope.expenseDtlEmpty)
                        },
                    }).then(function (success) {
                        $scope.$apply(function () {
                            console.log(success.data);
                           // $window.location.href = document.urlTravelRequest;
                        });
                    }, function (failure) {
                        console.log(failure);
                    });
                }
            }
        });


