(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("serviceQuestionList", function ($scope, $http) {
            $scope.serviceQuestionList = [];
            $scope.serviceEventTypeChange = function (serviceEventType) {
                console.log(serviceEventType);
                var serviceEventTypeId = serviceEventType;
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: "pullServiceQuestionList",
                    data: {
                        id: serviceEventTypeId
                    }
                }).then(function (response) {
                    App.unblockUI("#hris-page-content");
                    try {
                        if (!response.success) {
                            throw response.error;
                        }
                        $scope.$apply(function () {
                            console.log(response.data);
                            $scope.serviceQuestionList = response.data;
                        });
                    } catch (e) {
                        window.app.errorMessage(e, 'Error');
                    }
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    window.app.errorMessage(JSON.stringify(failure), "SYSTEM ERROR MESSAGE");
                });
            }

        });