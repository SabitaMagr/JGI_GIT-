(function ($, app) {
    $('#export').on("click", function () {
        app.exportDomToPdf2('paySlip');
    });

})(window.jQuery, window.app);

angular.module('hris', [])
        .controller('paySlipController', function ($scope) {
            $scope.payRollGeneratedMonths = [];
            $scope.monthId = null;
            $scope.rules = document.rules;
            $scope.addition = "A";
            $scope.deletion = "D";

            $scope.paySlip = null;

            $scope.fetchPayRollGeneratedMonths = function () {
                window.app.pullDataById(document.pullPayRollGeneratedMonthsLink, {
                    employeeId: document.employeeId
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
                window.app.pullDataById(document.fetchEmployeePaySlipLink, {
                    month: monthId
                }).then(function (success) {
                    $scope.$apply(function () {
                        $scope.paySlip = success.data;
                        console.log('payslip', $scope.paySlip);
                    });
                }, function (failure) {
                });
            };

        });