(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate',null,true);
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("attendanceStatusListController", function ($scope, $http) {
            var $tableContainer = $("#attendanceRequestStatusTable");
            var displayKendoFirstTime = true;
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var attendanceRequestStatusId = angular.element(document.getElementById('attendanceRequestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate')).val();
                var toDate = angular.element(document.getElementById('toDate')).val();
                var approverId = angular.element(document.getElementById('approverId')).val();
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
                        'toDate': toDate,
                        'approverId': approverId
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    if (displayKendoFirstTime) {
                        $scope.initializekendoGrid();
                        displayKendoFirstTime = false;
                    }
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#attendanceRequestStatusTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function () {
                $("#attendanceRequestStatusTable").kendoGrid({
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
//                    rowTemplate: kendo.template($("#rowTemplate").html()),
                    columns: [
                        {field: "FULL_NAME", title: "Employee" ,template: "<span>#: (FULL_NAME == null) ? '-' : FULL_NAME #</span>"},
                         {title: "Requested Date",
                    columns: [{
                            field: "REQUESTED_DT",
                            title: "English",
                            template: "<span>#: (REQUESTED_DT == null) ? '-' : REQUESTED_DT #</span>"},
                        {field: "REQUESTED_DT_N",
                            title: "Nepali",
                            template: "<span>#: (REQUESTED_DT_N == null) ? '-' : REQUESTED_DT_N #</span>"}]},
                  {title: "Attendance Date",
                    columns: [{
                            field: "ATTENDANCE_DT",
                            title: "English",
                            template: "<span>#: (ATTENDANCE_DT == null) ? '-' : ATTENDANCE_DT #</span>"},
                        {field: "ATTENDANCE_DT_N",
                            title: "Nepali",
                            template: "<span>#: (ATTENDANCE_DT_N == null) ? '-' : ATTENDANCE_DT_N #</span>"}]},
                        {field: "IN_TIME", title: "Check In" ,template: "<span>#: (IN_TIME == null) ? '-' : IN_TIME #</span>"},
                        {field: "OUT_TIME", title: "Check Out" ,template: "<span>#: (OUT_TIME == null) ? '-' : OUT_TIME #</span>"},
                        {field: "YOUR_ROLE", title: "Your Role" ,template: "<span>#: (YOUR_ROLE == null) ? '-' : YOUR_ROLE #</span>"},
                        {field: "STATUS", title: "Status" ,template: "<span>#: (STATUS == null) ? '-' : STATUS #</span>"},
                        {field: ["ID"], title: "Action", template: `<span> <a class="btn  btn-icon-only btn-success"
        href=" `+ document.viewLink +` /#: ID #" style="height:17px;" title="view">
        <i class="fa fa-search-plus"></i></a>
        </span>`}
                    ]
                });
                
                app.searchTable('attendanceRequestStatusTable',['FULL_NAME','REQUESTED_DT','ATTENDANCE_DT','REQUESTED_DT_N','ATTENDANCE_DT_N','IN_TIME','OUT_TIME','YOUR_ROLE','STATUS']);
                
                app.pdfExport(
                'attendanceRequestStatusTable',
                {
                    'FULL_NAME': 'Name',
                    'REQUESTED_DT': 'Req.Date(AD)',
                    'REQUESTED_DT_N': 'Req.Date(BS)',
                    'ATTENDANCE_DT': 'AttenDate(AD)',
                    'ATTENDANCE_DT_N': 'AttenDate(BS)',
                    'IN_TIME': 'In Time',
                    'OUT_TIME': 'Out Time',
                    'TOTAL_HOUR': 'Total Hrs',
                    'IN_REMARKS': 'In Remarks',
                    'OUT_REMARKS': 'Out Remarks',
                    'STATUS': 'Status',
                    'APPROVED_DT': 'Approved Date',
                    'APPROVED_REMARKS': 'Approved Remarks',
                    
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
                                {value: "Requested Date(AD)"},
                                {value: "Requested Date(BS)"},
                                {value: "Attendance Date(AD)"},
                                {value: "Attendance Date(BS)"},
                                {value: "Check In Time"},
                                {value: "Check Out Time"},
                                {value: "Total Hour"},
                                {value: "Late In Reason"},
                                {value: "Early Out Reason"},
                                {value: "Your Role"},
                                {value: "Status"},
                                {value: "Approved Date"},
                                {value: "Remarks By You"}
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
                        rows.push({
                            cells: [
                                {value: dataItem.FULL_NAME},
                                {value: dataItem.REQUESTED_DT},
                                {value: dataItem.REQUESTED_DT_N},
                                {value: dataItem.ATTENDANCE_DT},
                                {value: dataItem.ATTENDANCE_DT_N},
                                {value: dataItem.IN_TIME},
                                {value: dataItem.OUT_TIME},
                                {value: dataItem.TOTAL_HOUR},
                                {value: dataItem.IN_REMARKS},
                                {value: dataItem.OUT_REMARKS},
                                {value: "Approver"},
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
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true}
                                ],
                                title: "Attendance Request List",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AttendanceRequest.xlsx"});
                }
            };
        });