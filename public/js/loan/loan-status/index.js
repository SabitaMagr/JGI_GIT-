(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate1', 'nepaliToDate', 'toDate1',null,true);
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
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var loanId = angular.element(document.getElementById('loanId')).val();
                var loanRequestStatusId = angular.element(document.getElementById('loanRequestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate1')).val();
                var toDate = angular.element(document.getElementById('toDate1')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullLoanRequestStatusList',
                    data: {
                        'employeeId': employeeId,
                        'companyId':companyId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'loanId': loanId,
                        'loanRequestStatusId': loanRequestStatusId,
                        'fromDate': fromDate,
                        'toDate': toDate
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    console.log(success.data);
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
                        {field: "FULL_NAME", title: "Employee Name"},
                        {field: "LOAN_NAME", title: "Loan Name"},
//                        {field: "REQUESTED_DATE", title: "Requested Date", width: 150},
                          {title: "Requested Date",
                    columns: [{
                            field: "REQUESTED_DATE",
                            title: "AD",
                            template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                        {field: "REQUESTED_DATE_N",
                            title: "BS",
                            template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}]},
                  {title: "Loan Date",
                    columns: [{
                            field: "LOAN_DATE",
                            title: "AD",
                            template: "<span>#: (LOAN_DATE == null) ? '-' : LOAN_DATE #</span>"},
                        {field: "LOAN_DATE_N",
                            title: "BS",
                            template: "<span>#: (LOAN_DATE_N == null) ? '-' : LOAN_DATE_N #</span>"}]},
//                        {field: "LOAN_DATE", title: "Loan Date", width: 100},
                        {field: "REQUESTED_AMOUNT", title: "Requested Amt."},
                        {field: "RECOMMENDER_NAME", title: "Recommender"},
                        {field: "APPROVER_NAME", title: "Approver"},                        
                        {field: "STATUS", title: "Status"},
                        {field: [""], title: "Action", template: `<span><a class="btn-edit"
        href="`+ document.viewLink +`/#: LOAN_REQUEST_ID #" style="height:17px;" title="view">
        <i class="fa fa-search-plus"></i>
        </a></span>`}
                    ]
                });
                
                app.searchTable('loanRequestStatusTable',['FULL_NAME','LOAN_NAME','REQUESTED_DATE','LOAN_DATE', 'REQUESTED_DATE_N','LOAN_DATE_N','REQUESTED_AMOUNT','RECOMMENDER_NAME','APPROVER_NAME','STATUS']);
                
                 app.pdfExport(
                'loanRequestStatusTable',
                {
                    'FULL_NAME': 'Name',
                    'LOAN_NAME': 'Loan',
                    'REQUESTED_DATE': 'Request Date(AD)',
                    'REQUESTED_DATE_N': 'Request Date(BS)',
                    'LOAN_DATE': 'Loan Date(AD)',
                    'LOAN_DATE_N': 'Loan Date(BS)',
                    'REQUESTED_AMOUNT': 'Request Amt',
                    'RECOMMENDER_NAME': 'Recommender',
                    'APPROVER_NAME': 'Approver',
                    'STATUS': 'status',
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
                                {value: "Requested Amount"},
                                {value: "Recommender"},
                                {value: "Approver"},
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
                        var mn1 = dataItem.MN1 != null ? " " + dataItem.MN1 + " " : " ";
                        var mn2 = dataItem.MN2 != null ? " " + dataItem.MN2 + " " : " ";
                        rows.push({
                            cells: [
                                {value: dataItem.FULL_NAME},
                                {value: dataItem.LOAN_NAME},
                                {value: dataItem.REQUESTED_DATE},
                                {value: dataItem.REQUESTED_DATE_N},
                                {value: dataItem.LOAN_DATE},
                                {value: dataItem.LOAN_DATE_N},
                                {value: dataItem.REQUESTED_AMOUNT},
                                {value: dataItem.RECOMMENDER_NAME},
                                {value: dataItem.APPROVER_NAME},
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
                                    {autoWidth: true},
                                    {autoWidth: true}
                                ],
                                title: "Loan Request",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "LoanRequestList.xlsx"});
                }
               
                window.app.UIConfirmations();
            };
        });