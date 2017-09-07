(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate1', 'nepaliToDate', 'toDate1', null, true);
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("advanceStatusListController", function ($scope, $http) {
            var $tableContainer = $("#advanceRequestStatusTable");
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
                var advanceId = angular.element(document.getElementById('advanceId')).val();
                var advanceRequestStatusId = angular.element(document.getElementById('advanceRequestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate1')).val();
                var toDate = angular.element(document.getElementById('toDate1')).val();
                var employeeTypeId = angular.element(document.getElementById('employeeTypeId')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullAdvanceRequestStatusList',
                    data: {
                        'employeeId': employeeId,
                        'companyId': companyId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'advanceId': advanceId,
                        'advanceRequestStatusId': advanceRequestStatusId,
                        'fromDate': fromDate,
                        'toDate': toDate,
                        'employeeTypeId': employeeTypeId
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    console.log(success.data);
                    if (displayKendoFirstTime) {
                        $scope.initializekendoGrid();
                        displayKendoFirstTime = false;
                    }
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#advanceRequestStatusTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function () {
                $("#advanceRequestStatusTable").kendoGrid({
                    excel: {
                        fileName: "AdvanceRequestList.xlsx",
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
                        {field: "ADVANCE_NAME", title: "Advance Name"},
                        {title: "Requested Date",
                            columns: [{
                                    field: "REQUESTED_DATE",
                                    title: "AD",
                                    template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                                {field: "REQUESTED_DATE_N",
                                    title: "BS",
                                    template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}]},
                        {title: "AdvanceDate",
                            columns: [{
                                    field: "ADVANCE_DATE",
                                    title: "AD",
                                    template: "<span>#: (ADVANCE_DATE == null) ? '-' : ADVANCE_DATE #</span>"},
                                {field: "ADVANCE_DATE_N",
                                    title: "BS",
                                    template: "<span>#: (ADVANCE_DATE_N == null) ? '-' : ADVANCE_DATE_N #</span>"}]},
//                        {field: "REQUESTED_DATE", title: "Requested Date", width: 130},
//                        {field: "ADVANCE_DATE", title: "Advance Date", width: 120},
                        {field: "REQUESTED_AMOUNT", title: "Requested Amt."},
                        {field: "RECOMMENDER_NAME", title: "Recommender"},
                        {field: "APPROVER_NAME", title: "Approver"},
                        {field: "STATUS", title: "Status"},
                        {field: [""], title: "Action", template: `<span> <a class="btn-edit"
        href="` + document.viewLink + `/#: ADVANCE_REQUEST_ID #" style="height:17px;" title="view">
        <i class="fa fa-search-plus"></i>
        </a></span>`}
                    ]
                });

                app.searchTable('advanceRequestStatusTable', ['FULL_NAME', 'ADVANCE_NAME', 'REQUESTED_DATE', 'ADVANCE_DATE', 'REQUESTED_DATE_N', 'ADVANCE_DATE_N', 'REQUESTED_AMOUNT', 'RECOMMENDER_NAME', 'APPROVER_NAME', 'STATUS']);

                app.pdfExport(
                        'advanceRequestStatusTable',
                        {
                            'FULL_NAME': 'Name',
                            'ADVANCE_NAME': 'Advance',
                            'REQUESTED_AMOUNT': 'Request Amt',
                            'REQUESTED_DATE': 'Requested Date(AD)',
                            'REQUESTED_DATE_N': 'Requested Date(BS)',
                            'ADVANCE_DATE': 'Advance Date(AD)',
                            'ADVANCE_DATE_N': 'Advance Date(BS)',
                            'TERMS': 'Term',
                            'RECOMMENDER_NAME': 'Recommender',
                            'APPROVER_NAME': 'Approver',
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
                                {value: "Advance Name"},
                                {value: "Requested Amount"},
                                {value: "Requested Date(AD)"},
                                {value: "Requested Date(BS)"},
                                {value: "Advance Date(AD)"},
                                {value: "Advance Date(BS)"},
                                {value: "Terms"},
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
                    var dataSource = $("#advanceRequestStatusTable").data("kendoGrid").dataSource;
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
                                {value: dataItem.ADVANCE_NAME},
                                {value: dataItem.REQUESTED_DATE},
                                {value: dataItem.REQUESTED_DATE_N},
                                {value: dataItem.ADVANCE_DATE},
                                {value: dataItem.ADVANCE_DATE_N},
                                {value: dataItem.REQUESTED_AMOUNT},
                                {value: dataItem.TERMS},
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
                                    {autoWidth: true},
                                    {autoWidth: true}
                                ],
                                title: "Advance Request",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AdvanceRequestList.xlsx"});
                }

                window.app.UIConfirmations();
            };
        });