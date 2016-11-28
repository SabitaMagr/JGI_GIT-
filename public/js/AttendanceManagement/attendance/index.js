/**
 * Created by punam on 10/5/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.addDatePicker(
                $("#fromDate"),
                $("#toDate")
                );
    });
})(window.jQuery, window.app);


angular.module('hris', [])
        .controller('attendanceController', function ($scope, $http) {
            $scope.attendanceList = [];
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var fromDate = angular.element(document.getElementById('fromDate1')).val();
                var toDate = angular.element(document.getElementById('toDate1')).val();

                window.app.pullDataById(document.url, {
                    action: 'pullAttendanceList',
                    data: {
                        'fromDate': fromDate,
                        'toDate': toDate,
                        'employeeId': employeeId
                    }
                }).then(function (success) {
                    $scope.initializekendoGrid(success.data);
                }, function (failure) {
                    console.log(failure);
                });
            }

            $scope.initializekendoGrid = function (attendanceRecord) {
                $("#attendanceTable").kendoGrid({
                    dataSource: {
                        data: attendanceRecord,
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
                    rowTemplate: kendo.template($("#rowTemplate").html()),
                    columns: [
                        {field: "ATTENDANCE_DT", title: "Attendance Date"},
                        {field: "IN_TIME", title: "Check In"},
                        {field: "OUT_TIME", title: "Check Out"},
                        {field: "TOTAL_HOUR", title: "Total Hour"},
                        {field: "IN_REMARKS", title: "Late In Reason"},
                        {field: "OUT_REMARKS", title: "Early Out Reason"},
                    ]
                });
            }
        });