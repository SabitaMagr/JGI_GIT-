(function ($, app) {
    'use strict';
angular.module('hris', [])
        .controller('detailController', function ($scope, $http,$window) {
            $scope.overtimeDetailList = [];
            $scope.calculateHour = function(startTime,endTime){
                console.log(startTime);
                console.log(endTime);
                
                var tim_i = new Date("01/01/2007 " + startTime);
                var tim_o = new Date("01/01/2007 " + endTime);
                
                var diff1 = (tim_i - tim_o) / 60000; //dividing by seconds and milliseconds
                var diff = Math.abs(diff1);
                var minutes = diff % 60;
                var hours = (diff - minutes) / 60;

                var total_tim = hours + '.' + minutes;
                return total_tim;
            }
            $scope.overtimeDetailTemplate = {
                detailId : 0,
                startTime: "",
                endTime: "",
                totalHour:$scope.calculateHour(startTime,endTime),
                checkbox: "checkboxq0",
                checked: false
            };
            var overtimeId = parseInt(angular.element(document.getElementById('overtimeId')).val());
            if (overtimeId!==0) {
                window.app.pullDataById(document.urlOvertime, {
                        action:"pullOvertimeDetail",
                        data: {
                            'overtimeId': overtimeId
                        }
                    }).then(function (success) {
                        $scope.$apply(function () {
                           var tempData = success.data;
                           var overtimeDetailList = tempData.overtimeDetailList
                           var num = tempData.num;
                           if(num>0){
                                $scope.counter = num;
                                for (var j = 0; j < num; j++) {
                                    $scope.overtimeDetailList.push(angular.copy({
                                        detailId: overtimeDetailList[j].DETAIL_ID,
                                        startTime: overtimeDetailList[j].START_TIME,
                                        endTime: overtimeDetailList[j].END_TIME,
                                        totalHour: overtimeDetailList[j].TOTAL_HOUR,
                                        checkbox: "checkboxq" + j,
                                        checked: false
                                    }));
                                }
                           }else{
                               $scope.overtimeDetailList.push(angular.copy($scope.overtimeDetailTemplate));
                           }
                        });
                    },function(failure){
                        console.log(failure);
                    });
            }else{
                $scope.overtimeDetailList.push(angular.copy($scope.overtimeDetailTemplate));
            }
            
            $scope.counter = 1;
            $scope.addOvertimeDetail = function () {
                $scope.overtimeDetailList.push(angular.copy({
                    detailId:0,
                    startTime: "",
                    endTime: "",
                    totalHour:$scope.calculateHour(startTime,endTime),
                    checkbox: "checkboxq"+$scope.counter,
                    checked: false
                }));
                $scope.counter++;
            }
            
            $scope.delete = function () {
                var tempId = 0;
                var length = $scope.overtimeDetailList.length;
                for (var i = 0; i < length; i++) {
                    if ($scope.overtimeDetailList[i - tempId].checked) {
                        var detailId = $scope.overtimeDetailList[i - tempId].detailId;
                        if (detailId != 0) {
                            window.app.pullDataById(document.urlDeleteOvertimeDetail, {
                                data: {
                                    "detailId": detailId
                                }
                            }).then(function (success) {
                                $scope.$apply(function () {
                                    console.log(success.data);
                                });
                            }, function (failure) {
                                console.log(failure);
                            });
                        }
                        $scope.overtimeDetailList.splice(i - tempId, 1);
                        tempId++;
                    }
                }
            }
            console.log($scope.overtimeDetailList);
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