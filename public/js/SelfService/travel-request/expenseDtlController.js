(function ($, app) {
    'use strict';
    angular.module('hris', [])
            .controller('expenseDtlController', function ($scope, $http, $window) {
                $scope.expenseDtlFormList = [];
                $scope.counter = '';
                var l;
                $scope.transportTypeList = [
                    {"id": "AP", "name": "Flight"},
                    {"id": "OV", "name": "Office Vehicles"},
                    {"id": "TI", "name": "Taxi"},
                    {"id": "BS", "name": "Bus"},
                ];
                $scope.travelDetail = {
                    departureDateMain :'',
                    returnedDate:'',
                    approverRole:'CEO',
                    destination:'',
                    purpose:'',
                    advanceAmount:''
                };
                $scope.expenseDtlFormTemplate = {
                    id: 0,
                    departureDate: "",
                    departureTime: "",
                    departurePlace: "",
                    destinationDate: "",
                    destinationTime: "",
                    destinationPlace: "",
                    transportType: $scope.transportTypeList[0],
                    fare: 0,
                    allowance: 0,
                    localConveyence:0,
                    miscExpense: 0,
                    fareFlag:false,
                    allowanceFlag:false,
                    localConveyenceFlag:false,
                    miscExpenseFlag:false,
                    total: 0,
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
                            var tempData = success.data;
                            var travelDtl = tempData.travelDetail;
                            var expenseDtlList = tempData.expenseDtlList;
                            var num = tempData.numExpenseDtlList;
                            $scope.travelDetail.departureDateMain = travelDtl.DEPARTURE_DATE;
                            $scope.travelDetail.returnedDate=travelDtl.RETURNED_DATE;
                            $scope.travelDetail.approverRole=travelDtl.APPROVER_ROLE;
                            $scope.travelDetail.destination = travelDtl.DESTINATION;
                            $scope.travelDetail.purpose = travelDtl.PURPOSE;
                            $scope.travelDetail.advanceAmount = travelDtl.ADVANCE_AMOUNT;
                            if (num > 0) {
                                $scope.counter = num;
                                for (var j = 0; j < num; j++) {
                                    if (expenseDtlList[j].TRANSPORT_TYPE == 'AP') {
                                        var transportTypeSelected = $scope.transportTypeList[0];
                                    } else if (expenseDtlList[j].TRANSPORT_TYPE == 'OV') {
                                        var transportTypeSelected = $scope.transportTypeList[1];
                                    } else if (expenseDtlList[j].TRANSPORT_TYPE == 'TI') {
                                        var transportTypeSelected = $scope.transportTypeList[2];
                                    } else if (expenseDtlList[j].TRANSPORT_TYPE == 'BS') {
                                        var transportTypeSelected = $scope.transportTypeList[3];
                                    }
                                    $scope.expenseDtlFormList.push(angular.copy({
                                        id: expenseDtlList[j].ID,
                                        departureDate: expenseDtlList[j].DEPARTURE_DATE,
                                        departureTime: expenseDtlList[j].DEPARTURE_TIME,
                                        departurePlace: expenseDtlList[j].DEPARTURE_PLACE,
                                        destinationDate: expenseDtlList[j].DESTINATION_DATE,
                                        destinationTime: expenseDtlList[j].DESTINATION_TIME,
                                        destinationPlace: expenseDtlList[j].DESTINATION_PLACE,
                                        transportType: transportTypeSelected,
                                        fare: parseFloat(expenseDtlList[j].FARE),
                                        allowance: parseFloat(expenseDtlList[j].ALLOWANCE),
                                        localConveyence: parseFloat(expenseDtlList[j].LOCAL_CONVEYENCE),
                                        miscExpense: parseFloat(expenseDtlList[j].MISC_EXPENSES),
                                        fareFlag: (expenseDtlList[j].FARE_FLAG==='Y')?true:false,
                                        allowanceFlag: (expenseDtlList[j].ALLOWANCE_FLAG==='Y')?true:false,
                                        localConveyenceFlag: (expenseDtlList[j].LOCAL_CONVEYENCE_FLAG==='Y')?true:false,
                                        miscExpenseFlag: (expenseDtlList[j].MISC_EXPENSES_FLAG==='Y')?true:false,
                                        remarks: expenseDtlList[j].REMARKS,
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
                        fare: 0,
                        allowance: 0,
                        localConveyence:0,
                        miscExpense: 0,
                        fareFlag:false,
                        allowanceFlag:false,
                        localConveyenceFlag:false,
                        miscExpenseFlag:false,
                        total:0,
                        remarks: "",
                        checkbox: "checkboxt" + $scope.counter,
                        checked: false
                    }));
//                    $scope.expenseDtlFormList.total = $scope.total($scope.expenseDtlFormList.fare,$scope.expenseDtlFormList.allowance,$scope.expenseDtlFormList.localConveyence,$scope.expenseDtlFormList.miscExpense);
                    $scope.counter++;

                    l = Ladda.create(document.querySelector('#submitBtn'));
                };
                $scope.deleteExpenseDtl = function () {
                    var tempT = 0;
                    var lengthT = $scope.expenseDtlFormList.length;
                    for (var i = 0; i < lengthT; i++) {
                        if ($scope.expenseDtlFormList[i - tempT].checked) {
                            var id = $scope.expenseDtlFormList[i - tempT].id;
                            if (id != 0) {
                                window.app.pullDataById(document.urlDeleteExpenseDetail, {
                                    data: {
                                        "id": parseInt(id)
                                    }
                                }).then(function (success) {
                                    $scope.$apply(function () {
                                        console.log(success.data);
                                    });
                                }, function (failure) {
                                    console.log(failure);
                                });
                            }

                            $scope.expenseDtlFormList.splice(i - tempT, 1);
                            tempT++;
                        }
                    }
                }
                $scope.total = function(fare,allowance,localConveyence,miscExpense){
                    var fare1 = ( typeof fare=== 'undefined' || fare===null || isNaN(fare))?parseFloat(0) : parseFloat(fare);
                    var allowance1 = ( typeof allowance=== 'undefined' || allowance===null || isNaN(allowance)) ? parseFloat(0) : parseFloat(allowance);
                    var localConveyence1 = ( typeof localConveyence=== 'undefined' || localConveyence===null || isNaN(localConveyence))? parseFloat(0) : parseFloat(localConveyence);
                    var miscExpense1 =( typeof miscExpense=== 'undefined' || miscExpense===null || isNaN(miscExpense))? parseFloat(0) : parseFloat(miscExpense);
                    var total = fare1+allowance1+localConveyence1+miscExpense1;
                    return total || 0;
                }
                
                $scope.sumAllTotal = function(list) {
                    var total=0;
                    angular.forEach(list , function(item){
                      var total1 = $scope.total(item.fare,item.allowance,item.localConveyence,item.miscExpense);
                      total+= parseFloat(total1);
                    });
                    return total;
                }
                
                $scope.submitExpenseDtl = function () {
                    var sumAllTotal = parseFloat(angular.element(document.getElementById('sumAllTotal')).val());
                    if ($scope.travelExpenseForm.$valid && $scope.expenseDtlFormList.length > 0) {
                        l.start();
                        l.setProgress(0.5);
                        $scope.expenseDtlEmpty = 1;
                        if ($scope.expenseDtlFormList.length == 1 && angular.equals($scope.expenseDtlFormTemplate, $scope.expenseDtlFormList[0])) {
                            console.log("app log", "The form is not filled");
                            $scope.expenseDtlEmpty = 0;
                        }
//                        angular.forEach($scope.expenseDtlFormList , function(item){
//                            var id = item.checkbox;
//                            var totalId = "total_"+id;
//                            var totalValue = parseFloat(angular.element(document.getElementById(totalId)).val());
//                            item.total=totalValue;
//                        });
                        console.log(document.urlExpenseRequest);
                        window.app.pullDataById(document.urlExpenseRequest, {
                            data: {
                                expenseDtlList: $scope.expenseDtlFormList,
                                travelId: parseInt(travelId),
                                departureDate: $scope.travelDetail.departureDateMain,
                                returnedDate: $scope.travelDetail.returnedDate,
                                requestedType: requestedType,
                                sumAllTotal:sumAllTotal,
                                expenseDtlEmpty: parseInt($scope.expenseDtlEmpty),
                                approverRole:$scope.travelDetail.approverRole,
                                destination:$scope.travelDetail.destination,
                                purpose:$scope.travelDetail.purpose,
                                advanceAmount:$scope.travelDetail.advanceAmount
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
            }).directive("datepicker", function () {
        return {
            restrict: "A",
            require: "ngModel",
            link: function (scope, elem, attrs, ngModelCtrl) {
                $(elem).val(attrs.dvalue);
                app.addDatePicker($(elem));
            }
        }
    }).directive("select2", function () {
        return {
            restrict: "A",
            require: "ngModel",
            link: function (scope, elem, attrs, ngModelCtrl) {
                $(elem).select2();
            }
        }
    }).directive("timepicker", function () {
        return {
            restrict: "A",
            require: "ngModel",
            link: function (scope, elem, attrs, ngModelCtrl) {
                $(elem).val(attrs.dvalue);
                app.addTimePicker($(elem));
            }
        }
    });
})(window.jQuery, window.app);

