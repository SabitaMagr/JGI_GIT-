(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate',null,true);
        
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
        .controller("attendanceRequestListController", function ($scope, $http) {
            var $tableContainer = $("#attendanceRequestTable");
            var displayKendoFirstTime = true;
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var attendanceRequestStatusId = angular.element(document.getElementById('attendanceRequestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate')).val();
                var toDate = angular.element(document.getElementById('toDate')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullAttendanceRequestList',
                    data: {
                        'employeeId': employeeId,
                        'attendanceRequestStatusId': attendanceRequestStatusId,
                        'fromDate': fromDate,
                        'toDate': toDate
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    if (displayKendoFirstTime) {
                        $scope.initializekendoGrid();
                        displayKendoFirstTime = false;
                    }
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#attendanceRequestTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                    window.app.UIConfirmations();
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function () {
                $("#attendanceRequestTable").kendoGrid({
                    excel: {
                        fileName: "AttendanceRequestList.xlsx",
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
                        {field: "REQUESTED_DT", title: "Applied Date", width: 120},
                        {field: "ATTENDANCE_DT", title: "Attendance Date", width: 140},
                        {field: "IN_TIME", title: "Check In", width: 100},
                        {field: "OUT_TIME", title: "Check Out", width: 100},
                        {field: "A_STATUS", title: "Status", width: 100},
                        {title: "Action", width: 80}
                    ]
                });
                
                app.searchTable('attendanceRequestTable',['REQUESTED_DT','ATTENDANCE_DT','IN_TIME','OUT_TIME','A_STATUS']);
                
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
                                {value: "Applied Date"},
                                {value: "Attendance Date"},
                                {value: "Check In"},
                                {value: "Check Out"},
                                {value: "Late In Reason"},
                                {value: "Early Out Reason"},
                                {value: "Total Hour"},
                                {value: "Status"},
                                {value: "Approver"},
                                {value: "Remarks By Approver"},
                                {value: "Approved Date"},
                            ]
                        }];
                    var dataSource = $("#attendanceRequestTable").data("kendoGrid").dataSource;
                    var filteredDataSource = new kendo.data.DataSource({
                        data: dataSource.data(),
                        filter: dataSource.filter()
                    });

                    filteredDataSource.read();
                    var data = filteredDataSource.view();

                    for (var i = 0; i < data.length; i++) {
                        var dataItem = data[i];
                        rows.push({
                            cells: [
                                {value: dataItem.REQUESTED_DT},
                                {value: dataItem.ATTENDANCE_DT},
                                {value: dataItem.IN_TIME},
                                {value: dataItem.OUT_TIME},
                                {value: dataItem.IN_REMARKS},
                                {value: dataItem.OUT_REMARKS},
                                {value: dataItem.TOTAL_HOUR},
                                {value: dataItem.STATUS},
                                {value: dataItem.APPROVER_NAME},
                                {value: dataItem.APPROVED_REMARKS},
                                {value: dataItem.APPROVED_DT}
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
                                    {autoWidth: true}
                                ],
                                title: "Attendance Request",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AttendanceRequestList.xlsx"});
                }
            };
        });
