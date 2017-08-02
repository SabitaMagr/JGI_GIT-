(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
    });
})(window.jQuery, window.app);

angular.module('hris', ["ui.multiselect"])
        .controller('monthlyValueDetailController', function ($scope, $http) {
            $scope.monthlyValues = document.monthlyValues;

        });