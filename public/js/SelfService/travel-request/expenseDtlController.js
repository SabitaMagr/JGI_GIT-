angular.module('hris', [])
        .controller('expenseDtlController', function ($scope, $http, $window) {
            $scope.expenseDtlFormList = [];
            $scope.counter = '';
            var l;
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
                miscExpense: "",
                total: "",
                remarks: "",
                checkbox: "checkboxt0",
                checked: false
            };
            
            l = Ladda.create(document.querySelector('#submitBtn'));
            var travelId = parseInt(angular.element(document.getElementById('travelId')).val());
            var requestedType = angular.element(document.getElementById('requestedType')).val();
            console.log(requestedType);
            if (requestedType == 'ep') {
                window.app.pullDataById(document.urlExpenseDetailList, {
                    data: {
                        'travelId': travelId
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        var trainingList = success.data;
                        var trainingNum = success.num;
                        console.log(trainingList);
                        if (trainingNum > 0) {
                            $scope.counter = trainingNum;
                            for (var j = 0; j < trainingNum; j++) {
                                $scope.trainingFormList.push(angular.copy({
                                    id: trainingList[j].ID,
                                    trainingName: trainingList[j].TRAINING_NAME,
                                    description: trainingList[j].DESCRIPTION,
                                    fromDate: trainingList[j].FROM_DATE,
                                    toDate: trainingList[j].TO_DATE,
                                    checkbox: "checkboxt" + j,
                                    checked: false
                                }));
                            }
                        } else {
                            $scope.counter = 1;
                            $scope.expenseDtlFormList.push(angular.copy($scope.expenseDtlFormTemplate));
                        }
                    });
                }, function (failure) {
                    console.log(failure);
                });
            } else if (requestedType == 'ad') {
                $scope.counter = 1;
                $scope.expenseDtlFormList.push(angular.copy($scope.expenseDtlFormTemplate));
            }
            
            $scope.addExpenseDtl = function () {
                $scope.expenseDtlFormList.push(angular.copy({
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
                    miscExpense: "",
                    total: "",
                    remarks: "",
                    checkbox: "checkboxt" + $scope.counter,
                    checked: false
                }));
                $scope.counter++;

                l = Ladda.create(document.querySelector('#submitBtn'));
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
            $scope.submitExpenseDtl = function () {
                if ($scope.travelExpenseForm.$valid && $scope.expenseDtlFormList.length > 0) {
                    l.start();
                    l.setProgress(0.5);
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
                            returnedDate: $scope.returnedDate,
                            expenseDtlEmpty: parseInt($scope.expenseDtlEmpty)
                        },
                    }).then(function (success) {
                        $scope.$apply(function () {
                            l.stop();
                            console.log(success.data);
                            var tempData = success.data;
                            $window.location.href = document.urlTravelRequest;
                            $window.localStorage.setItem("msg", tempData.msg);
                        });
                    }, function (failure) {
                        console.log(failure);
                    });
                }
            }
        });


