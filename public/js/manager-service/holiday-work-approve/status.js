(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("holidayWorkStatusListController", function ($scope, $http) {
            var $tableContainer = $("#holidayWorkRequestStatusTable");
            var displayKendoFirstTime = true;
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
                var holidayId = angular.element(document.getElementById('holidayId')).val();
                var requestStatusId = angular.element(document.getElementById('requestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate')).val();
                var toDate = angular.element(document.getElementById('toDate')).val();
                var recomApproveId = angular.element(document.getElementById('recomApproveId')).val();
                var employeeTypeId = angular.element(document.getElementById('employeeTypeId')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullHoliayWorkRequestStatusList',
                    data: {
                        'employeeId': employeeId,
                        'companyId': companyId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'holidayId': holidayId,
                        'requestStatusId': requestStatusId,
                        'fromDate': fromDate,
                        'toDate': toDate,
                        'recomApproveId': recomApproveId,
                        'employeeTypeId': employeeTypeId
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    console.log(success.recomApproveId);
                    if (displayKendoFirstTime) {
                        $scope.initializekendoGrid();
                        displayKendoFirstTime = false;
                    }
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#holidayWorkRequestStatusTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function () {
                $("#holidayWorkRequestStatusTable").kendoGrid({
                    excel: {
                        fileName: "HolidayWorkRequestList.xlsx",
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
                        {field: "FULL_NAME", title: "Employee"},
                        {field: "HOLIDAY_ENAME", title: "Holiday"},
                        {title: "Requested Date",
                            columns: [{
                                    field: "REQUESTED_DATE",
                                    title: "English",
                                    template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                                {field: "REQUESTED_DATE_N",
                                    title: "Nepali",
                                    template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}]},
                        {title: "From Date",
                            columns: [{
                                    field: "FROM_DATE",
                                    title: "English",
                                    template: "<span>#: (FROM_DATE == null) ? '-' : FROM_DATE #</span>"},
                                {field: "FROM_DATE_N",
                                    title: "Nepali",
                                    template: "<span>#: (FROM_DATE_N == null) ? '-' : FROM_DATE_N #</span>"}]},
                        {title: "To Date",
                            columns: [{
                                    field: "TO_DATE",
                                    title: "English",
                                    template: "<span>#: (TO_DATE == null) ? '-' : TO_DATE #</span>"},
                                {field: "TO_DATE_N",
                                    title: "Nepali",
                                    template: "<span>#: (TO_DATE_N == null) ? '-' : TO_DATE_N #</span>"}]},
                        {field: "DURATION", title: "Duration"},
                        {field: "YOUR_ROLE", title: "Your Role"},
                        {field: "STATUS", title: "Status"},
                        {field: ["ID"], title: "Action", template: `<span> <a class="btn  btn-icon-only btn-success"
        href=" ` + document.viewLink + `/#: ID #/#: ROLE #" style="height:17px;" title="view">
        <i class="fa fa-search-plus"></i></a>
        </span>`}
                    ]
                });

                app.searchTable('holidayWorkRequestStatusTable', ['FULL_NAME', 'HOLIDAY_ENAME', 'REQUESTED_DATE', 'FROM_DATE', 'TO_DATE', 'REQUESTED_DATE_N', 'FROM_DATE_N', 'TO_DATE_N', 'DURATION', 'YOUR_ROLE', 'STATUS']);

                app.pdfExport(
                        'holidayWorkRequestStatusTable',
                        {
                            'FULL_NAME': 'Name',
                            'HOLIDAY_ENAME': 'Holiday',
                            'REQUESTED_DATE': 'Request Date(AD)',
                            'REQUESTED_DATE_N': 'Request Date(BS)',
                            'FROM_DATE': 'From Date(AD)',
                            'FROM_DATE_N': 'From Date(BS)',
                            'TO_DATE': 'To Date(AD)',
                            'TO_DATE_N': 'To Date(BS)',
                            'DURATION': 'Duration',
                            'YOUR_ROLE': 'Role',
                            'STATUS': 'Status',
                            'REMARKS': 'Remarks',
                            'RECOMMENDED_REMARKS': 'Recommended Remarks',
                            'RECOMMENDED_DATE': 'Recommended Date',
                            'APPROVED_REMARKS': 'Approved Remarks',
                            'APPROVED_DATE': 'Approved Date'

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
                                {value: "Holiday Name"},
                                {value: "Requested Date(AD)"},
                                {value: "Requested Date(BS)"},
                                {value: "From Date(AD)"},
                                {value: "From Date(BS)"},
                                {value: "To Date(AD)"},
                                {value: "To Date(BS)"},
                                {value: "Your Role"},
                                {value: "Duration"},
                                {value: "Status"},
                                {value: "Remarks"},
                                {value: "Remarks By Recommender"},
                                {value: "Recommended Date"},
                                {value: "Remarks By Approver"},
                                {value: "Approved Date"}
                            ]
                        }];
                    var dataSource = $("#holidayWorkRequestStatusTable").data("kendoGrid").dataSource;
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
                                {value: dataItem.HOLIDAY_ENAME},
                                {value: dataItem.REQUESTED_DATE},
                                {value: dataItem.REQUESTED_DATE_N},
                                {value: dataItem.FROM_DATE},
                                {value: dataItem.FROM_DATE_N},
                                {value: dataItem.TO_DATE},
                                {value: dataItem.TO_DATE_N},
                                {value: dataItem.YOUR_ROLE},
                                {value: dataItem.DURATION},
                                {value: dataItem.STATUS},
                                {value: dataItem.REMARKS},
                                {value: dataItem.RECOMMENDED_REMARKS},
                                {value: dataItem.RECOMMENDED_DATE},
                                {value: dataItem.APPROVED_REMARKS},
                                {value: dataItem.APPROVED_DATE}
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
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true}
                                ],
                                title: "Work on Holiday Request List",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "HolidayWorkRequestList.xlsx"});
                }
            };
        });