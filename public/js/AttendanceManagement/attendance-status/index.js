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
        .controller("attendanceStatusListController", function ($scope, $http) {

            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var attendanceRequestStatusId = angular.element(document.getElementById('attendanceRequestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate1')).val();
                var toDate = angular.element(document.getElementById('toDate1')).val();

                window.app.pullDataById(document.url, {
                    action: 'pullAttendanceRequestStatusList',
                    data: {
                        'employeeId': employeeId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
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
            $scope.initializekendoGrid = function (attendanceRequestStatus) {
                $("#attendanceRequestStatusTable").kendoGrid({
                    dataSource: {
                        data: attendanceRequestStatus,
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
                    dataBound:gridDataBound,                   
                    rowTemplate: kendo.template($("#rowTemplate").html()),
                    columns: [
                        {field: "FIRST_NAME", title: "Employee Name", width: 200},
                        {field: "ATTENDANCE_DT", title: "Attendance Date", width: 160},
                        {field: "IN_TIME", title: "Check In", width: 120},
                        {field: "OUT_TIME", title: "Check Out", width: 120},
                        {field: "IN_REMARKS", title: "Late In Reason", width: 200},
                        {field: "OUT_REMARKS", title: "Early Out Reason", width: 200},
                        {field: "APPROVER", title: "Approver", width: 180},
                        {field: "STATUS", title: "Status", width: 100},
                        {title: "Action", width: 100}
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