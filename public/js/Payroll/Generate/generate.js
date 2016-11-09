// (function ($, app) {
//     'use strict';
//     $(document).ready(function () {
//         var generateBtn = $('#generateBtn');
//         var employeeCb = $('#employeeId');
//
//         generateBtn.on('click', function (e) {
//             app.pullDataById(document.url, {
//                 action: 'generataMonthlySheet',
//                 data: {employee: employeeCb.val()}
//             }).then(function (success) {
//                 console.log(success);
//             }, function (failure) {
//                 console.log(failure);
//             });
//         });
//
//     });
// })(window.jQuery, window.app);


angular.module('hris', [])
    .controller('generateController', function ($scope) {
        var generateBtn = angular.element(document.querySelector('#generateBtn'));

        $scope.rules = document.rules;
        $scope.employeeList = document.employeeList;

        $scope.employeeId;
        $scope.employeeRuleValues = {};

        generateBtn.on('click', function (e) {
            window.app.pullDataById(document.url, {
                action: 'generataMonthlySheet',
                data: {
                    employee: (($scope.employeeId === null) || (typeof $scope.employeeId === 'undefined')) ? -1 : $scope.employeeId,
                }
            }).then(function (success) {
                $scope.$apply(function () {
                    console.log(success);
                    $scope.employeeRuleValues = success.data;
                });
            }, function (failure) {
                console.log(failure);
            });
        });

    });