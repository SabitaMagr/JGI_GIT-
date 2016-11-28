/**
 * Created by root on 10/18/16.
 */
angular.module('hris', [])
    .controller('ruleSetupController', function ($scope, $http) {
        $scope.rule={
            payCode:"",
            payEdesc:"",
            payLdesc:"",
            payTypeFlag:'A',
            priorityIndex:0,
            remarks:""
        };

    });
