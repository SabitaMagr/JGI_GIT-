(function ($, app) {

})(window.jQuery, window.app);

angular.module('hris', [])
        .controller('paySlipController', function ($scope) {
            $scope.payRollGeneratedMonths = [];
            $scope.monthId = null;
            $scope.rules = document.rules;
            console.log("rules", $scope.rules);

            $scope.fetchPayRollGeneratedMonths = function () {
                window.app.pullDataById(document.restfulUrl, {
                    action: 'pullPayRollGeneratedMonths',
                    data: {
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        console.log("pullPayRollGeneratedMonths res", success);
                        $scope.payRollGeneratedMonths = success.data;
                    });
                }, function (failure) {
                    console.log("pullPayRollGeneratedMonths fail", failure);
                });
            };
            $scope.fetchPayRollGeneratedMonths();

            $scope.changeMonths = function (monthId) {
                window.app.pullDataById(document.restfulUrl, {
                    action: 'fetchEmployeePaySlip',
                    data: {
                        month: monthId
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        console.log("fetchEmployeePaySlip res", success);
                    });
                }, function (failure) {
                    console.log("fetchEmployeePaySlip fail", failure);
                });
            };

        });