(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("loanStatusListController", function ($scope, $http) {
            var $tableContainer = $("#loanRequestStatusTable");
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
                var loanId = angular.element(document.getElementById('loanId')).val();
                var loanRequestStatusId = angular.element(document.getElementById('loanRequestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate')).val();
                var toDate = angular.element(document.getElementById('toDate')).val();
                var recomApproveId = angular.element(document.getElementById('recomApproveId')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullLoanRequestStatusList',
                    data: {
                        'employeeId': employeeId,
                        'companyId': companyId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'loanId': loanId,
                        'loanRequestStatusId': loanRequestStatusId,
                        'fromDate': fromDate,
                        'toDate': toDate,
                        'recomApproveId': recomApproveId
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    console.log(success.recomApproveId);
                    if (displayKendoFirstTime) {
                        $scope.initializekendoGrid();
                        displayKendoFirstTime = false;
                    }
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#loanRequestStatusTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function () {
                $("#loanRequestStatusTable").kendoGrid({
                    excel: {
                        fileName: "LoanRequestList.xlsx",
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
                        {field: "FULL_NAME", title: "Employee", width: 200},
                        {field: "LOAN_NAME", title: "Loan", width: 120},
                        {title: "Requested Date",
                            columns: [{
                                    field: "REQUESTED_DATE",
                                    title: "English",
                                    template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                                {field: "REQUESTED_DATE_N",
                                    title: "Nepali",
                                    template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}]},
                        {title: "Loan Date",
                            columns: [{
                                    field: "LOAN_DATE",
                                    title: "English",
                                    template: "<span>#: (LOAN_DATE == null) ? '-' : LOAN_DATE #</span>"},
                                {field: "LOAN_DATE_N",
                                    title: "Nepali",
                                    template: "<span>#: (LOAN_DATE_N == null) ? '-' : LOAN_DATE_N #</span>"}]},
                        {field: "REQUESTED_AMOUNT", title: "Requested Amount", width: 150},
                        {field: "YOUR_ROLE", title: "Your Role", width: 150},
                        {field: "STATUS", title: "Status", width: 90},
                        {field: ["LOAN_REQUEST_ID"], title: "Action", template: `<span>  <a class="btn  btn-icon-only btn-success"
        href="` + document.viewLink + `/#: LOAN_REQUEST_ID #/#: ROLE #" style="height:17px;" title="view">
        <i class="fa fa-search-plus"></i></a>
        </span>`}
                    ]
                });

                app.searchTable('loanRequestStatusTable', ['FULL_NAME', 'LOAN_NAME', 'REQUESTED_DATE', 'LOAN_DATE', 'REQUESTED_DATE_N', 'LOAN_DATE_N', 'REQUESTED_AMOUNT', 'YOUR_ROLE', 'STATUS']);

                app.pdfExport(
                        'loanRequestStatusTable',
                        {
                            'FULL_NAME': 'Name',
                            'LOAN_NAME': 'Loan',
                            'REQUESTED_DATE': 'Request Date(AD)',
                            'REQUESTED_DATE_N': 'Request Date(BS)',
                            'LOAN_DATE': 'Loan Date(AD)',
                            'LOAN_DATE_N': 'Loan Date(BS)',
                            'REQUESTED_AMOUNT': 'Reqest Amt',
                            'YOUR_ROLE': 'Role',
                            'STATUS': 'Status',
                            'REASON': 'Reason',
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
                                {value: "Loan Name"},
                                {value: "Requested Date(AD)"},
                                {value: "Requested Date(BS)"},
                                {value: "Loan Date(AD)"},
                                {value: "Loan Date(BS)"},
                                {value: "Your Role"},
                                {value: "Requested Amount"},
                                {value: "Status"},
                                {value: "Reason"},
                                {value: "Remarks By Recommender"},
                                {value: "Recommended Date"},
                                {value: "Remarks By Approver"},
                                {value: "Approved Date"}
                            ]
                        }];
                    var dataSource = $("#loanRequestStatusTable").data("kendoGrid").dataSource;
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
                                {value: dataItem.LOAN_NAME},
                                {value: dataItem.REQUESTED_DATE},
                                {value: dataItem.REQUESTED_DATE_N},
                                {value: dataItem.LOAN_DATE},
                                {value: dataItem.LOAN_DATE_N},
                                {value: dataItem.YOUR_ROLE},
                                {value: dataItem.REQUESTED_AMOUNT},
                                {value: dataItem.STATUS},
                                {value: dataItem.REASON},
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
                                    {autoWidth: true}
                                ],
                                title: "Loan Request List",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "LoanRequestList.xlsx"});
                }
            };
        });