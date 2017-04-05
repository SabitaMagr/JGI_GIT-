(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate');
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("attendanceListController", function ($scope, $http) {
            var $tableContainer = $("#attendanceByHrTable");
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
                var fromDate = angular.element(document.getElementById('fromDate')).val();
                var toDate = angular.element(document.getElementById('toDate')).val();
                var status = angular.element(document.getElementById('statusId')).val();
                $tableContainer.block();
                window.app.pullDataById(document.url, {
                    action: 'pullAttendanceList',
                    data: {
                        'employeeId': employeeId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'fromDate': fromDate,
                        'toDate': toDate,
                        'status': status
                    }
                }).then(function (success) {
                    $tableContainer.unblock();
                    console.log(success.data);
                    $scope.initializekendoGrid(success.data);
                }, function (failure) {
                    $tableContainer.unblock();
                    console.log(failure);
                });
            };

            $scope.initializekendoGrid = function (attendanceList) {
                $("#attendanceByHrTable").kendoGrid({
                    excel: {
                        fileName: "AttendanceList.xlsx",
                        filterable: true,
                        allPages: true
                    },
                    dataSource: {
                        data: attendanceList,
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
                        {field: "FIRST_NAME", title: "Employee Name", width: 160},
                        {field: "ATTENDANCE_DT", title: "Attendance Date", width: 120},
                        {field: "IN_TIME", title: "Check In", width: 100},
                        {field: "OUT_TIME", title: "Check Out", width: 120},
                        {field: "STATUS", title: "Status", width: 150},
                        {title: "Action", width: 80}
                    ]
                });
            };
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
                            {value: "Attendance Date"},
                            {value: "Check In Time"},
                            {value: "Check Out Time"},
                            {value: "Late In Reason"},
                            {value: "Late Out Reason"},
                            {value: "Total Hour"},
                            {value: "Status"}
                        ]
                    }];
                var dataSource = $("#attendanceByHrTable").data("kendoGrid").dataSource;
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
                            {value: dataItem.ATTENDANCE_DT},
                            {value: dataItem.IN_TIME},
                            {value: dataItem.OUT_TIME},
                            {value: dataItem.IN_REMARKS},
                            {value: dataItem.OUT_REMARKS},
                            {value: dataItem.TOTAL_HOUR},
                            {value: dataItem.STATUS}
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
                                {autoWidth: true}
                            ],
                            title: "Attendance Report",
                            rows: rows
                        }
                    ]
                });
                kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AttendanceList.xlsx"});
            }

            window.app.UIConfirmations();

        });
