/**
 * Created by punam on 9/19/16.
 */


angular.module('hris', [])
        .controller('holidayController', function ($scope, $http,$window) {
            $scope.holidayDtl = {
                holidayCode: '',
                genderId: '',
                holidayEname: '',
                holidayLname: '',
                startDate: '',
                endDate: '',
                halfday: '',
                remarks: ''
            };

            var holidayId = angular.element(document.getElementById('holidayId'));
            var branchId = angular.element(document.getElementById('branchId'));

            var getHolidayDetail = function () {
                var holidayIdValue = holidayId.val();
                window.app.pullDataById(document.url, {
                    action: 'pullHolidayDetail',
                    id: holidayIdValue
                }).then(function (success) {
                    $scope.$apply(function () {
                        var temp = success.data;
                        $scope.holidayDtl.holidayCode = temp.HOLIDAY_CODE;
                        if (temp.GENDER_ID == null) {
                            $scope.holidayDtl.genderId = -1;
                        } else {
                            $scope.holidayDtl.genderId = temp.GENDER_ID;
                        }
                        $scope.holidayDtl.holidayEname = temp.HOLIDAY_ENAME;
                        $scope.holidayDtl.holidayLname = temp.HOLIDAY_LNAME;
                        $scope.holidayDtl.startDate = temp.START_DATE;
                        $scope.holidayDtl.endDate = temp.END_DATE;
                        $scope.holidayDtl.halfday = temp.HALFDAY;
                        $scope.holidayDtl.remarks = temp.REMARKS;
                    });
                }, function (failure) {
                    console.log(failure);
                });

                window.app.pullDataById(document.urlBranchList, {
                    id: holidayIdValue
                }).then(function (data) {
                    $scope.$apply(function () {
                        var valArray = [];
                        for (key in data) {
                            valArray.push(key);
                        }
                        branchId.val(valArray).trigger("change");
                    });
                }, function (failure) {
                    console.log(failure);
                });
            }
            holidayId.on("change", getHolidayDetail);
            getHolidayDetail();

            $scope.update = function () {
                if ($scope.holidayForm.$valid) {
                    var err = [];
                    $(".errorMsg").each(function () {
                        var erroMsg = $.trim($(this).html());
                        if (erroMsg !== "") {
                            err.push("error");
                        }
                    });
                    if (err.length > 0)
                    {
                        return;
                    }
                    
                    var holidayId = angular.element(document.getElementById('holidayId')).val();
                    var branchIdValue = branchId.val();
                    App.blockUI({target: "#hris-page-content"});
                    window.app.pullDataById(document.url, {
                        action: 'updateHolidayDetail',
                        data: {
                            holidayId: holidayId,
                            dataArray: $scope.holidayDtl,
                            branchIds: branchIdValue
                        },
                    }).then(function (success) {
                        $scope.$apply(function () {

                            var data = {id: 1, text: "hellow"};
                            var val = document.getElementById('holidayId');

                            document.holidays = document.holidays.map(function (item) {
                                if (item.id == val.value) {
                                    item.text = $scope.holidayDtl.holidayEname;
                                    item.selected = true;
                                }
                                return item;
                            });

//                            $('#holidayId').text("");
//                            $('#holidayId').select2({
//                                data: document.holidays
//                            });
                            App.unblockUI("#hris-page-content");
                            $window.location.href =  document.urlIndex;
                            $window.localStorage.setItem("msg",success.data);
                        });
                    }, function (failure) {
                        App.unblockUI("#hris-page-content");
                        console.log(failure);
                    });
                }
            };
        });
