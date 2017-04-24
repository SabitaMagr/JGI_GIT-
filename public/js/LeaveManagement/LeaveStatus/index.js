(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
//        app.startEndDatePicker("fromDate", "toDate");
        app.startEndDatePickerWithNepali("nepaliFromDate1", "fromDate1", "nepaliToDate1", "toDate1");
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("leaveStatusListController", function ($scope, $http) {
            var $tableContainer = $("#leaveRequestStatusTable");
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var leaveId = angular.element(document.getElementById('leaveId')).val();
                var leaveRequestStatusId = angular.element(document.getElementById('leaveRequestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate1')).val();
                var toDate = angular.element(document.getElementById('toDate1')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullLeaveRequestStatusList',
                    data: {
                        'employeeId': employeeId,
                        'companyId':companyId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'leaveId': leaveId,
                        'leaveRequestStatusId': leaveRequestStatusId,
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
            $scope.initializekendoGrid = function (leaveRequestStatus) {
                $("#leaveRequestStatusTable").kendoGrid({
                    excel: {
                        fileName: "LeaveRequestList.xlsx",
                        filterable: true,
                        allPages: true
                    },
                    dataSource: {
                        data: leaveRequestStatus,
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
                        {field: "FIRST_NAME", title: "Employee Name", width: 150},
                        {field: "LEAVE_ENAME", title: "Leave Name", width: 120},
                        {field: "APPLIED_DATE", title: "Requested Date", width: 130},
                        {field: "START_DATE", title: "From Date", width: 100},
                        {field: "END_DATE", title: "To Date", width: 90},
                        {field: "RECOMMENDER_NAME", title: "Recommender", width: 120},
                        {field: "APPRVOER_NAME", title: "Approver", width: 120},
                        {field: "NO_OF_DAYS", title: "Duration", width: 90},
                        {field: "STATUS", title: "Status", width: 80},
                        {title: "Action", width: 70}
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
                                {value: "Leave Name"},
                                {value: "Requested Date"},
                                {value: "From Date"},
                                {value: "To Date"},
                                {value: "Recommender"},
                                {value: "Approver"},
                                {value: "Duration"},
                                {value: "Status"},
                                {value: "Remarks By Employee"},
                                {value: "Remarks By Recommender"},
                                {value: "Recommended Date"},
                                {value: "Remarks By Approver"},
                                {value: "Approved Date"}
                            ]
                        }];
                    var dataSource = $("#leaveRequestStatusTable").data("kendoGrid").dataSource;
                    var filteredDataSource = new kendo.data.DataSource({
                        data: dataSource.data(),
                        filter: dataSource.filter()
                    });

                    filteredDataSource.read();
                    var data = filteredDataSource.view();

                    for (var i = 0; i < data.length; i++) {
                        var dataItem = data[i];
                        var middleName = dataItem.MIDDLE_NAME != null ? " " + dataItem.MIDDLE_NAME + " " : " ";
                        var mn1 = dataItem.MN1 != null ? " " + dataItem.MN1 + " " : " ";
                        var mn2 = dataItem.MN2 != null ? " " + dataItem.MN2 + " " : " ";
                        rows.push({
                            cells: [
                                {value: dataItem.FIRST_NAME + middleName + dataItem.LAST_NAME},
                                {value: dataItem.LEAVE_ENAME},
                                {value: dataItem.APPLIED_DATE},
                                {value: dataItem.START_DATE},
                                {value: dataItem.END_DATE},
                                {value: dataItem.RECOMMENDER_NAME},
                                {value: dataItem.APPROVER_NAME},
                                {value: dataItem.NO_OF_DAYS},
                                {value: dataItem.STATUS},
                                {value: dataItem.REMARKS},
                                {value: dataItem.RECOMMENDED_REMARKS},
                                {value: dataItem.RECOMMENDED_DT},
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
                                    {autoWidth: true},
                                    {autoWidth: true}
                                ],
                                title: "Leave Request",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "LeaveRequestList.xlsx"});
                }

                window.app.UIConfirmations();
            };
        });