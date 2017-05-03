(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
//        app.startEndDatePicker('fromDate', 'toDate');
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate');
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("attendanceStatusListController", function ($scope, $http) {
            var $tableContainer = $("#attendanceRequestStatusTable");
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
                var attendanceRequestStatusId = angular.element(document.getElementById('attendanceRequestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate')).val();
                var toDate = angular.element(document.getElementById('toDate')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullAttendanceRequestStatusList',
                    data: {
                        'employeeId': employeeId,
                        'companyId':companyId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'attendanceRequestStatusId': attendanceRequestStatusId,
                        'fromDate': fromDate,
                        'toDate': toDate
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    $scope.initializekendoGrid(success.data);
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function (attendanceRequestStatus) {
                $("#attendanceRequestStatusTable").kendoGrid({
                    excel: {
                        fileName: "AttendanceRequestList.xlsx",
                        filterable: true,
                        allPages: true
                    },
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
                    dataBound: gridDataBound,
                    rowTemplate: kendo.template($("#rowTemplate").html()),
                    columns: [
                        {field: "FIRST_NAME", title: "Employee", width: 200},
                        {field: "REQUESTED_DT", title: "Requested Date", width: 200},
                        {field: "ATTENDANCE_DT", title: "Attendance Date", width: 160},
                        {field: "IN_TIME", title: "Check In", width: 120},
                        {field: "OUT_TIME", title: "Check Out", width: 120},
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
                }
                ;
                $("#export").click(function (e) {
                    var rows = [{
                            cells: [
                                {value: "Employee Name"},
                                {value: "Requested Date"},
                                {value: "Attendance Date"},
                                {value: "Check In Time"},
                                {value: "Check Out Time"},
                                {value: "Total Hour"},
                                {value: "Late In Reason"},
                                {value: "Early Out Reason"},
                                {value: "Approver Name"},
                                {value: "Status"},
                                {value: "Approved Date"},
                                {value: "Remarks By Approver"}
                            ]
                        }];
                    var dataSource = $("#attendanceRequestStatusTable").data("kendoGrid").dataSource;
                    var filteredDataSource = new kendo.data.DataSource({
                        data: dataSource.data(),
                        filter: dataSource.filter()
                    });

                    filteredDataSource.read();
                    var data = filteredDataSource.view();

                    for (var i = 0; i < data.length; i++) {
                        var dataItem = data[i];
                        var middleName = dataItem.MIDDLE_NAME != null ? " " + dataItem.MIDDLE_NAME + " " : " ";
                        rows.push({
                            cells: [
                                {value: dataItem.FIRST_NAME + middleName + dataItem.LAST_NAME},
                                {value: dataItem.REQUESTED_DT},
                                {value: dataItem.ATTENDANCE_DT},
                                {value: dataItem.IN_TIME},
                                {value: dataItem.OUT_TIME},
                                {value: dataItem.TOTAL_HOUR},
                                {value: dataItem.IN_REMARKS},
                                {value: dataItem.OUT_REMARKS},
                                {value: dataItem.APPROVER_NAME},
                                {value: dataItem.STATUS},
                                {value: dataItem.APPROVED_DT},
                                {value: dataItem.APPROVED_REMARKS}
                            ]
                        });
                    }
                    excelExport(rows);
                    e.preventDefault();
                });

                function excelExport(rows) {
                    var workbook = new kendo.ooxml.Workbook({
                        sheets: [
                            {
                                columns: [
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true}
                                ],
                                title: "Attendance Request List",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AttendanceRequestList.xlsx"});
                }
                window.app.UIConfirmations();
            };
        });