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
        .controller("attendanceRequestListController", function ($scope, $http) {

            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var attendanceRequestStatusId = angular.element(document.getElementById('attendanceRequestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate1')).val();
                var toDate = angular.element(document.getElementById('toDate1')).val();

                window.app.pullDataById(document.url, {
                    action: 'pullAttendanceRequestList',
                    data: {
                        'employeeId': employeeId,
                        'attendanceRequestStatusId': attendanceRequestStatusId,
                        'fromDate': fromDate,
                        'toDate': toDate
                    }
                }).then(function (success) {
                    $scope.initializekendoGrid(success.data);
                }, function (failure) {
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function (attendanceRequestList) {
                $("#attendanceRequestTable").kendoGrid({
                    dataSource: {
                        data: attendanceRequestList,
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
                        {field: "ATTENDANCE_DT", title: "Attendance Date"},
                        {field: "IN_TIME", title: "Check In"},
                        {field: "OUT_TIME", title: "Check Out"},
                        {field: "IN_REMARKS", title: "Late In Reason"},
                        {field: "OUT_REMARKS", title: "Early Out Reason"},
                        {field: "A_STATUS", title: "Status"},
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
                }
                ;
            };
        });
