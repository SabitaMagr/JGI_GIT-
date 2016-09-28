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
                    $scope.holidayDtl.genderId = temp.GENDER_ID;
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

                    // $.each(data, function( key, value ) {
                    //    valArray.push({id:key,text:value,selected:true});
                    //     $("#branchId option[value='" + key + "']").prop("selected", true);
                    //
                    // });

                    for ( key in data){
                       valArray.push(key);
                    }
                    console.log(valArray);
                    var multiSelect = branchId.select2();
                   branchId.val(valArray);
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
            console.log(branchIdValue);
            window.app.pullDataById(document.url, {
                action: 'updateHolidayDetail',
                data: {
                    holidayId: holidayId,
                    dataArray: $scope.holidayDtl,
                    branchIds:branchIdValue
                },
            }).then(function (success) {
                $scope.$apply(function () {
                    document.getElementById('holidayId').options[document.getElementById('holidayId').selectedIndex].text=$scope.holidayDtl.holidayEname;
                    window.app.notification(success.data, {position: "top right", className: "success"})
                });
            }, function (failure) {
                console.log(failure);
            });
        };
    });
