(function ($, app) {
    'use strict';
    angular.module('hris', [])
            .controller('detailController', function ($scope, $http, $window) {
                $scope.overtimeDetailList = [];
                $scope.calculateHour = function (startTime, endTime) {
                    var hrs = Number(startTime.match(/^(\d+)/)[1]);
                    var mnts = Number(startTime.match(/:(\d+)/)[1]);
                    var format = startTime.match(/\s(.*)$/)[1];
                    if (format == "PM" && hrs < 12)
                        hrs = hrs + 12;
                    if (format == "AM" && hrs == 12)
                        hrs = hrs - 12;
                    var hours = hrs.toString();
                    var minutes = mnts.toString();
                    if (hrs < 10)
                        hours = "0" + hours;
                    if (mnts < 10)
                        minutes = "0" + minutes;
                    //alert(hours + ":" + minutes);

                    var date1 = new Date();
                    date1.setHours(hours);
                    date1.setMinutes(minutes);

                    var hrs = Number(endTime.match(/^(\d+)/)[1]);
                    var mnts = Number(endTime.match(/:(\d+)/)[1]);
                    var format = endTime.match(/\s(.*)$/)[1];
                    if (format == "PM" && hrs < 12)
                        hrs = hrs + 12;
                    if (format == "AM" && hrs == 12)
                        hrs = hrs - 12;
                    var hours = hrs.toString();
                    var minutes = mnts.toString();
                    if (hrs < 10)
                        hours = "0" + hours;
                    if (mnts < 10)
                        minutes = "0" + minutes;
                    //alert(hours+ ":" + minutes);

                    var date2 = new Date();
                    date2.setHours(hours);
                    date2.setMinutes(minutes);

                    var diff = date2.getTime() - date1.getTime();
                    var hours = Math.floor(diff / (1000 * 60 * 60));
                    diff -= hours * (1000 * 60 * 60);
                    var mins = Math.floor(diff / (1000 * 60));
                    diff -= mins * (1000 * 60);

                    if (hours <= -1) {
                        hours += 24;
                    }
                    var total_tim = hours + ':' + ("0" + mins).slice(-2);
                    return total_tim;
                }

                $scope.totalCalculateHour = function (startTime, endTime) {
                    var hrs = Number(startTime.match(/^(\d+)/)[1]);
                    var mnts = Number(startTime.match(/:(\d+)/)[1]);
                    var format = startTime.match(/\s(.*)$/)[1];
                    if (format == "PM" && hrs < 12)
                        hrs = hrs + 12;
                    if (format == "AM" && hrs == 12)
                        hrs = hrs - 12;
                    var hours = hrs.toString();
                    var minutes = mnts.toString();
                    if (hrs < 10)
                        hours = "0" + hours;
                    if (mnts < 10)
                        minutes = "0" + minutes;
                    //alert(hours + ":" + minutes);

                    var date1 = new Date();
                    date1.setHours(hours);
                    date1.setMinutes(minutes);

                    var hrs = Number(endTime.match(/^(\d+)/)[1]);
                    var mnts = Number(endTime.match(/:(\d+)/)[1]);
                    var format = endTime.match(/\s(.*)$/)[1];
                    if (format == "PM" && hrs < 12)
                        hrs = hrs + 12;
                    if (format == "AM" && hrs == 12)
                        hrs = hrs - 12;
                    var hours = hrs.toString();
                    var minutes = mnts.toString();
                    if (hrs < 10)
                        hours = "0" + hours;
                    if (mnts < 10)
                        minutes = "0" + minutes;
                    //alert(hours+ ":" + minutes);

                    var date2 = new Date();
                    date2.setHours(hours);
                    date2.setMinutes(minutes);

                    var diff = date2.getTime() - date1.getTime();
                    var hours = Math.floor(diff / (1000 * 60 * 60));
                    diff -= hours * (1000 * 60 * 60);
                    var mins = Math.floor(diff / (1000 * 60));
                    diff -= mins * (1000 * 60);

                    if (hours <= -1) {
                        hours += 24;
                    }
                    
                    var total = {
                        hour: hours,
                        minute: mins
                    };
                    return total;

//                    var diff1 = (tim_i - tim_o) / 60000; //dividing by seconds and milliseconds
//                    var diff = Math.abs(diff1);
//                    return diff;
                }

                $scope.sumAllTotalHour = function (list) {
                    var totalHour = 0;
                    var totalMinute = 0;
                    angular.forEach(list, function (item) {
                        var total1 = $scope.totalCalculateHour(item.startTime, item.endTime);
                        totalHour += parseFloat(total1.hour);
                        totalMinute += parseFloat(total1.minute);
                        console.log(totalHour, totalMinute);
                    });
                    var minutes = totalMinute % 60;
                    var formattedMinute = ("0" + minutes).slice(-2);
                    var hours = parseInt(totalHour + totalMinute/60);

                    var total_tim = hours + ':' + formattedMinute;
                    return total_tim;
                }


                $scope.overtimeDetailTemplate = {
                    detailId: 0,
                    startTime: "",
                    endTime: "",
                };
                var overtimeId = parseInt(angular.element(document.getElementById('overtimeId')).val());
                if (overtimeId !== 0) {
                    window.app.pullDataById(document.urlOvertime, {
                        action: "pullOvertimeDetail",
                        data: {
                            'overtimeId': overtimeId
                        }
                    }).then(function (success) {
                        $scope.$apply(function () {
                            var tempData = success.data;
                            var overtimeDetailList = tempData.overtimeDetailList
                            var num = tempData.num;
                            if (num > 0) {
                                $scope.counter = num;
                                for (var j = 0; j < num; j++) {
                                    $scope.overtimeDetailList.push(angular.copy({
                                        detailId: overtimeDetailList[j].DETAIL_ID,
                                        startTime: overtimeDetailList[j].START_TIME,
                                        endTime: overtimeDetailList[j].END_TIME,
                                    }));
                                }
                            } else {
                                $scope.overtimeDetailList.push(angular.copy($scope.overtimeDetailTemplate));
                            }
                        });
                    }, function (failure) {
                        console.log(failure);
                    });
                } else {
                    $scope.overtimeDetailList.push(angular.copy($scope.overtimeDetailTemplate));
                }

                $scope.counter = 1;
                $scope.addOvertimeDetail = function () {
                    $scope.overtimeDetailList.push(angular.copy({
                        detailId: 0,
                        startTime: "",
                        endTime: "",
                    }));
                    $scope.counter++;
                }

                $scope.delete = function (index) {
                    console.log(index);
                    var detailItem = $scope.overtimeDetailList[index];
                    if (detailItem.detailId != 0) {
                        window.app.pullDataById(document.urlDeleteOvertimeDetail, {
                            data: {
                                "detailId": detailItem.detailId
                            }
                        }).then(function (success) {
                            $scope.$apply(function () {
                                $scope.overtimeDetailList.splice(index, 1);
                            });
                        }, function (failure) {
                            console.log(failure);
                        });
                    } else {
                        $scope.overtimeDetailList.splice(index, 1);
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