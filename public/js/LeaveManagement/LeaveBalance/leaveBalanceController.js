/**
 * Created by root on 11/7/16.
 */
angular.module('hris',[])
    .controller('leaveBalanceController',function($scope,$http){
        $scope.view=function(){
            var employeeId = angular.element(document.getElementById('employeeId')).val();
            var branchId = angular.element(document.getElementById('branchId')).val();
            var departmentId = angular.element(document.getElementById('departmentId')).val();
            var designationId = angular.element(document.getElementById('designationId')).val();
            var positionId = angular.element(document.getElementById('positionId')).val();
            var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
            var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();

            //console.log(employeeId+","+branchId+","+departmentId+","+designationId+","+positionId+","+serviceTypeId);

            window.app.pullDataById(document.url, {
                action: 'pullLeaveBalanceDetail',
                data: {
                    'employeeId':employeeId ,
                    'branchId':branchId,
                    'departmentId': departmentId,
                    'designationId':designationId,
                    'positionId':positionId,
                    'serviceTypeId':serviceTypeId,
                    'serviceEventTypeId':serviceEventTypeId
                }
            }).then(function (success) {
                $scope.$apply(function () {
                    $scope.allList = success.allList;
                    $scope.num = success.num;

                    console.log(success.allList);
                });
            }, function (failure) {
                console.log(failure);
            });

        };
});