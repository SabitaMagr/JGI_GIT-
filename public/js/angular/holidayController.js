/**
 * Created by punam on 9/19/16.
 */


angular.module('hris', [])
    .controller('holidayController', function ($scope, $http) {
        $scope.holidayDtl={
            holidayCode:'',
            genderId:'',
            holidayEname:'',
            holidayLname:'',
            startDate:'',
            endDate:'',
            halfday:'',
            remarks:''
        };
        $scope.filter = function(){
            var holidayId = $scope.holidayDtl.holidayId;

            window.app.pullDataById(document.url, {
                action: 'pullHolidayDetail',
                id:holidayId
            }).then(function (success) {
                $scope.$apply(function(){
                       var temp= success.data;
                    $scope.holidayDtl.holidayCode=temp.HOLIDAY_CODE;
                    $scope.holidayDtl.genderId=temp.GENDER_ID;
                    $scope.holidayDtl.holidayEname=temp.HOLIDAY_ENAME;
                    $scope.holidayDtl.holidayLname=temp.HOLIDAY_LNAME;
                    $scope.holidayDtl.startDate=temp.START_DATE;
                    $scope.holidayDtl.endDate=temp.END_DATE;
                    $scope.holidayDtl.halfday=temp.HALFDAY;
                    $scope.holidayDtl.remarks=temp.REMARKS;

                });
            }, function (failure) {
                console.log(failure);
            });
        };

        $scope.update = function(){
            var holidayId = angular.element(document.getElementById('holidayId')).val();

            window.app.pullDataById(document.url, {
                action: 'updateHolidayDetail',
                data:{
                    holidayId:holidayId,
                    dataArray:$scope.holidayDtl
                },
            }).then(function (success) {
                $scope.$apply(function(){
                    alert(success.data);
                });
            }, function (failure) {
                console.log(failure);
            });
        };
    });
