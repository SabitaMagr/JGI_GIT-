(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.addDatePicker(
                $("#fromDate"),
                $("#toDate")
                );
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("leaveRequestListController", function ($scope, $http) {
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var leaveId = angular.element(document.getElementById('leaveId')).val();
                var leaveRequestStatusId = angular.element(document.getElementById('leaveRequestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate1')).val();
                var toDate = angular.element(document.getElementById('toDate1')).val();

                window.app.pullDataById(document.url, {
                    action: 'pullLeaveRequestList',
                    data: {
                        'employeeId': employeeId,
                        'leaveId': leaveId,
                        'leaveRequestStatusId': leaveRequestStatusId,
                        'fromDate': fromDate,
                        'toDate': toDate
                    }
                }).then(function (success) {
                    $scope.initializekendoGrid(success.data);
                }, function (failure) {
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function (leaveRequest) {
                $("#leaveRequestTable").kendoGrid({
                    dataSource: {
                        data: leaveRequest,
                        pageSize: 20
                    },
                    height: 450,
                    scrollable: true,
                    sortable: true,
                    filterable: true,
                    pageable: {
                        input: true,
                        numeric: false
                    },
                    dataBound: gridDataBound,
                    rowTemplate: kendo.template($("#rowTemplate").html()),
                    columns: [
                        {field: "LEAVE_CODE", title: "Leave Code"},
                        {field: "LEAVE_ENAME", title: "Leave Name"},
                        {field: "FROM_DATE", title: "From Date"},
                        {field: "TO_DATE", title: "To Date"},
                        {field: "NO_OF_DAYS", title: "Duration"},
                        {field: "STATUS", title: "Status"},
                        {title: "Action"}
                    ]
                });
                function gridDataBound(e) {
                    var grid = e.sender;
                    if (grid.dataSource.total() == 0) {
                        var colCount = grid.columns.length;
                        $(e.sender.wrapper)
                                .find('tbody')
                                .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
                    }
                };

            };
        });