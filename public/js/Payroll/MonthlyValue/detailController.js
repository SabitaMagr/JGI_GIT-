/**
 * Created by ukesh on 10/4/16.
 */
angular.module('hris', [])
    .controller('monthlyValueDetailController', function ($scope, $http) {
        $scope.branches = document.branches;
        $scope.departments = document.departments;
        $scope.designations = document.designations;
        $scope.monthlyValuesI = document.monthlyValues;
        $scope.monthlyValues = [];
        for (var index in $scope.monthlyValuesI) {
            $scope.monthlyValues.push({id: index, text: $scope.monthlyValuesI[index], selected: false});
        }

        $scope.branch;
        $scope.department;
        $scope.designation;

        $scope.monthlyValuekeys=[];
        $scope.tableData;
       $scope.selectAllMonthlyValue=function(allMonthlyValue){
           for (var index in $scope.monthlyValues) {
               $scope.monthlyValues[index].selected=allMonthlyValue;
           }
       };

       $scope.view=function () {
           $scope.monthlyValues.filter(function(monthlyValue){
               if(monthlyValue.selected){
                  $scope. monthlyValuekeys.push(monthlyValue.id);
               }
           });
           window.app.pullDataById(document.url, {
               action: 'pullEmployeeMonthlyValue',
               id: {
                   branch: (typeof $scope.branch==='undefined')?-1:$scope.branch,
                   department: (typeof $scope.department==='undefined')?-1:$scope.department,
                   designation: (typeof $scope.designation==='undefined')?-1:$scope.designation,
                   monthlyValues:$scope.monthlyValuekeys
               }
           }).then(function (success) {
               console.log(success);
               $scope.$apply(function () {
                $scope.tableData=success.data;
               });

           }, function (failure) {
               console.log("failure",failure);

           });
       };


    });