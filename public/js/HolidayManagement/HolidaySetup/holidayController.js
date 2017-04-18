angular.module('hris', [])
        .controller('holidayController', function ($scope, $http, $window) {
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
            var designationId = angular.element(document.getElementById('designationId'));

            var getHolidayDetail = function () {
                var holidayIdValue = holidayId.val();

                window.app.pullDataById(document.pullHolidayDetailWS, {
                    id: holidayIdValue
                }).then(function (response) {
                    try {
                        if (!response.success) {
                            throw response.error;
                        }

                        $scope.$apply(function () {
                            var temp = response.data;
                            $scope.holidayDtl.holidayCode = temp.HOLIDAY_CODE;
                            if (temp.GENDER_ID === null) {
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

                    } catch (e) {
                        window.app.errorMessage(e, 'Error');
                    }
                }, function (failure) {
                    window.app.errorMessage(JSON.stringify(failure), "SYSTEM ERROR MESSAGE");
                });

                window.app.pullDataById(document.urlBranchList, {
                    id: holidayIdValue
                }).then(function (response) {
                    try {
                        if (!response.success) {
                            throw response.error;
                        }
                        var data = response.data;
                        $scope.$apply(function () {
                            var valArray = [];
                            for (k in data) {
                                valArray.push(k);
                            }
                            branchId.val(valArray).trigger("change");
                        });

                    } catch (e) {
                        window.app.errorMessage(e, 'Error');
                    }


                }, function (failure) {
                    window.app.errorMessage(JSON.stringify(failure), "SYSTEM ERROR MESSAGE");
                });


                window.app.pullDataById(document.urlDepartmentList, {
                    id: holidayIdValue
                }).then(function (response) {
                    try {
                        if (!response.success) {
                            throw response.error;
                        }
                        var data = response.data;
                        $scope.$apply(function () {
                            var valArray = [];
                            for (k in data) {
                                valArray.push(k);
                            }
                            designationId.val(valArray).trigger("change");
                        });

                    } catch (e) {
                        window.app.errorMessage(e, 'Error');
                    }
                }, function (failure) {
                    window.app.errorMessage(JSON.stringify(failure), "SYSTEM ERROR MESSAGE");
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
                    var designationIdValue = designationId.val();
                    App.blockUI({target: "#hris-page-content"});
                    window.app.pullDataById(document.updateHolidayDetailWS, {
                        data: {
                            holidayId: holidayId,
                            dataArray: $scope.holidayDtl,
                            branchIds: branchIdValue,
                            designationIds: designationIdValue
                        },
                    }).then(function (response) {

                        try {
                            if (!response.success) {
                                throw response.error;
                            }
                            $scope.$apply(function () {
                                var val = document.getElementById('holidayId');
                                document.holidays = document.holidays.map(function (item) {
                                    if (item.id === val.value) {
                                        item.text = $scope.holidayDtl.holidayEname;
                                        item.selected = true;
                                    }
                                    return item;
                                });

                                App.unblockUI("#hris-page-content");
                                $window.location.href = document.urlIndex;
                                $window.localStorage.setItem("msg", response.data);
                            });

                        } catch (e) {
                            window.app.errorMessage(e, 'Error');
                            App.unblockUI("#hris-page-content");
                        }

                    }, function (failure) {
                        App.unblockUI("#hris-page-content");
                        window.app.errorMessage(JSON.stringify(failure), "SYSTEM ERROR MESSAGE");
                    });
                }
            };
        });
