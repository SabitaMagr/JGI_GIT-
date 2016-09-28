/**
 * Created by punam on 9/19/16.
 */


angular.module('hris', [])
    .controller('holidayController', function ($scope, $http) {
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
        var branchId =angular.element(document.getElementById('branchId'));

        var getHolidayDetail = function(){
            var holidayIdValue = holidayId.val();
            window.app.pullDataById(document.url, {
                action: 'pullHolidayDetail',
                id: holidayIdValue
            }).then(function (success) {
                $scope.$apply(function () {
                    var temp = success.data;
                    $scope.holidayDtl.holidayCode = temp.HOLIDAY_CODE;
                    if(temp.GENDER_ID==null){
                        $scope.holidayDtl.genderId =-1;
                    }else {
                        $scope.holidayDtl.genderId =temp.GENDER_ID;
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
                    for ( key in data){
                       valArray.push(key);
                    }
                    branchId.val(valArray).trigger("change");
                });
            }, function (failure) {
                console.log(failure);
            });
        }
        holidayId.on("change",getHolidayDetail);
        getHolidayDetail();

        $scope.update = function () {
            var holidayId = angular.element(document.getElementById('holidayId')).val();
            var branchIdValue = branchId.val();

            console.log($scope.holidayDtl);
            window.app.pullDataById(document.url, {
                action: 'updateHolidayDetail',
                data: {
                    holidayId: holidayId,
                    dataArray: $scope.holidayDtl,
                    branchIds:branchIdValue
                },
            }).then(function (success) {
                $scope.$apply(function () {
                   // document.getElementById('holidayId').options[document.getElementById('holidayId').selectedIndex].text=$scope.holidayDtl.holidayEname;
                   // var holidayEname = $scope.holidayDtl.holidayEname;
                   //  console.log(holidayEname);
                    //angular.element(document.getElementById('holidayId')).text("holiday").trigger("change");
                   // $("#holidayId").select2('data', { id:holidayId, text:$scope.holidayDtl.holidayEname}).trigger("change");
                    window.toastr.info(success.data, "Notifications");
                });
            }, function (failure) {
                console.log(failure);
            });
        };
    });
