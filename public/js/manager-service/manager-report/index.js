/**
 * Created by punam on 10/5/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
//        console.log(document.currentEmployeeId);
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });
    });
})(window.jQuery, window.app);


angular.module('hris', [])
        .controller('attendanceController', function ($scope, $http) {
            $scope.attendanceList = [];
            var $tableContainer = $("#attendanceTable");
            var displayKendoFirstTime = true;
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var fromDate = angular.element(document.getElementById('fromDate')).val();
                var toDate = angular.element(document.getElementById('toDate')).val();
                var status = angular.element(document.getElementById('statusId')).val();
                var missPunchOnly = 0;
                if (($("#missPunchOnly").is(":checked"))) {
                    missPunchOnly = 1;
                }

                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullManagerAttendaceReport',
                    data: {
                        'currentEmployee': document.currentEmployeeId,
                        'fromDate': fromDate,
                        'toDate': toDate,
                        'employeeId': employeeId,
                        'status': status,
                        'missPunchOnly': missPunchOnly
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    if (displayKendoFirstTime) {
                        $scope.initializekendoGrid();
                        displayKendoFirstTime = false;
                    }
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#attendanceTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }

            $scope.initializekendoGrid = function () {
                $("#attendanceTable").kendoGrid({
                    excel: {
                        fileName: "AttendanceList.xlsx",
                        filterable: true,
                        allPages: true
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
                        {field: "FULL_NAME", title: "Employee"},
                        {field: "ATTENDANCE_DT", title: "Attendance Date"},
                        {field: "IN_TIME", title: "Check In"},
                        {field: "OUT_TIME", title: "Check Out"},
                        {field: "TOTAL_HOUR", title: "Total Hour"},
                        {field: "STATUS", title: "Status"},
                        {field: "IN_REMARKS", title: "Late In Reason"},
                        {field: "OUT_REMARKS", title: "Early Out Reason"},
                    ]
                });

                app.searchTable('attendanceTable', ['FULL_NAME','ATTENDANCE_DT', 'IN_TIME', 'OUT_TIME', 'TOTAL_HOUR', 'STATUS', 'IN_REMARKS', 'OUT_REMARKS']);
                app.pdfExport(
                        'attendanceTable',
                        {
                            'FULL_NAME': 'Employee',
                            'ATTENDANCE_DT': ' Attendance Date',
                            'IN_TIME': 'In Time',
                            'OUT_TIME': 'Out Time',
                            'TOTAL_HOUR': 'Total Hour',
                            'STATUS': 'Status',
                            'IN_REMARKS': 'In Remarks',
                            'OUT_REMARKS': 'Out Remarks'
                        }
                );
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
                    var grid = $("#attendanceTable").data("kendoGrid");
                    grid.saveAsExcel();
                });
            }


            



        });