(function ($, app) {
    $('#export').on("click", function () {
//        html2canvas($("#paySlip"), {
//            onrendered: function (canvas) {
//                console.log("canvas", canvas);
//                var imgData = canvas.toDataURL("image/jpeg", 1.0);
//                var pdf = new jsPDF();
//
//                pdf.addImage(imgData, 'JPEG', 15, 40, 180, 160);
//                pdf.save("download.pdf");
//
//            }
//        });
        app.exportDomToPdf('paySlip', document.cssUrl);
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
                    });
                }, function (failure) {
                });
            };

        });