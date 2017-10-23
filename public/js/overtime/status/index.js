(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate1', 'nepaliToDate', 'toDate1', null, true);
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("overtimeStatusListController", function ($scope, $http) {
            var $tableContainer = $("#overtimeRequestStatusTable");
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
                var requestStatusId = angular.element(document.getElementById('requestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate1')).val();
                var toDate = angular.element(document.getElementById('toDate1')).val();
                var employeeTypeId = angular.element(document.getElementById('employeeTypeId')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.pullOvertimeRequestStatusListLink, {
                    'employeeId': employeeId,
                    'companyId': companyId,
                    'branchId': branchId,
                    'departmentId': departmentId,
                    'designationId': designationId,
                    'positionId': positionId,
                    'serviceTypeId': serviceTypeId,
                    'serviceEventTypeId': serviceEventTypeId,
                    'requestStatusId': requestStatusId,
                    'fromDate': fromDate,
                    'toDate': toDate,
                    'employeeTypeId': employeeTypeId
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    console.log(success.data);
                    if (displayKendoFirstTime) {
                        $scope.initializekendoGrid();
                        displayKendoFirstTime = false;
                    }
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#overtimeRequestStatusTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function () {
                $("#overtimeRequestStatusTable").kendoGrid({
                    excel: {
                        fileName: "OvertimeRequestList.xlsx",
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
//                        {field: "REQUESTED_DATE", title: "Requested Date", width: 130},
//                        {field: "OVERTIME_DATE", title: "Overtime Date", width: 120},
                        {title: "Requested Date",
                            columns: [{
                                    field: "REQUESTED_DATE",
                                    title: "AD",
                                    template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                                {field: "REQUESTED_DATE_N",
                                    title: "BS",
                                    template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}]},
                        {title: "Overtime Date",
                            columns: [{
                                    field: "OVERTIME_DATE",
                                    title: "AD",
                                    template: "<span>#: (OVERTIME_DATE == null) ? '-' : OVERTIME_DATE #</span>"},
                                {field: "OVERTIME_DATE_N",
                                    title: "BS",
                                    template: "<span>#: (OVERTIME_DATE_N == null) ? '-' : OVERTIME_DATE_N #</span>"}]},
                        {field: "DETAILS", title: "Time (From-To)", template: `<ul id="branchList">  
        #  ln=DETAILS.length #
        #for(var i=0; i<ln; i++) { #
        <li>
        #=i+1 #) #=DETAILS[i].START_TIME # - #=DETAILS[i].END_TIME #
        </li>
        #}#
        </ul>`},
                        {field: "TOTAL_HOUR", title: "Total Hour", type: "number"},
                        {field: "RECOMMENDER_NAME", title: "Recommender"},
                        {field: "APPROVER_NAME", title: "Approver"},
                        {field: "STATUS", title: "Status"},
                        {field: ["OVERTIME_ID"], title: "Action", template: `<span><a class="btn-edit"
        href="` + document.viewLink + `/#: OVERTIME_ID #" style="height:17px;" title="view">
        <i class="fa fa-search-plus"></i>
        </a></span>`}
                    ]
                });

                app.searchTable('overtimeRequestStatusTable', ['FULL_NAME', 'REQUESTED_DATE', 'OVERTIME_DATE', 'REQUESTED_DATE_N', 'OVERTIME_DATE_N', 'TOTAL_HOUR', 'RECOMMENDER_NAME', 'APPROVER_NAME', 'STATUS']);

                app.pdfExport(
                        'overtimeRequestStatusTable',
                        {
                            'FULL_NAME': 'Name',
                            'REQUESTED_DATE': 'Request Date(AD)',
                            'REQUESTED_DATE_N': 'Request Date(BS)',
                            'OVERTIME_DATE': 'Overtime Date(AD)',
                            'OVERTIME_DATE_N': 'Overtime Date(BS)',
                            'TOTAL_HOUR': 'Total Hour',
                            'DESCRIPTION': 'Description',
                            'RECOMMENDER_NAME': 'Recommender',
                            'APPROVER_NAME': 'Approver',
                            'STATUS': 'Status',
                            'REMARKS': 'Remarks',
                            'RECOMMENDED_REMARKS': 'Recommender Remarks',
                            'RECOMMENDED_DATE': 'Recommended Date',
                            'APPROVED_REMARKS': 'Approver Remarks',
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
                var exportAction = function (e) {
                    var rows = [{
                            cells: [
                                {value: "Employee Name"},
                                {value: "Requested Date(AD)"},
                                {value: "Requested Date(BS)"},
                                {value: "Overtime Date(AD)"},
                                {value: "Overtime Date(BS)"},
                                {value: "Time (From-To)"},
                                {value: "Total Hour"},
                                {value: "Description"},
                                {value: "Recommender"},
                                {value: "Approver"},
                                {value: "Status"},
                                {value: "Remarks"},
                                {value: "Remarks By Recommender"},
                                {value: "Recommended Date"},
                                {value: "Remarks By Approver"},
                                {value: "Approved Date"}
                            ]
                        }];
                    var dataSource = $("#overtimeRequestStatusTable").data("kendoGrid").dataSource;
                    var filteredDataSource = new kendo.data.DataSource({
                        data: dataSource.data(),
                        filter: dataSource.filter()
                    });

                    filteredDataSource.read();
                    var data = filteredDataSource.view();
                    var totalHours = 0.0;
                    for (var i = 0; i < data.length; i++) {
                        var dataItem = data[i];
                        var mn1 = dataItem.MN1 != null ? " " + dataItem.MN1 + " " : " ";
                        var mn2 = dataItem.MN2 != null ? " " + dataItem.MN2 + " " : " ";
                        var details = [];
                        for (var j = 0; j < dataItem.DETAILS.length; j++) {
                            details.push(dataItem.DETAILS[j].START_TIME + "-" + dataItem.DETAILS[j].END_TIME);
                        }
                        var details1 = details.toString();
                        rows.push({
                            cells: [
                                {value: dataItem.FULL_NAME},
                                {value: dataItem.REQUESTED_DATE},
                                {value: dataItem.REQUESTED_DATE_N},
                                {value: dataItem.OVERTIME_DATE},
                                {value: dataItem.OVERTIME_DATE_N},
                                {value: details1},
                                {value: parseFloat(dataItem.TOTAL_HOUR)},
                                {value: dataItem.DESCRIPTION},
                                {value: dataItem.RECOMMENDER_NAME},
                                {value: dataItem.APPROVER_NAME},
                                {value: dataItem.STATUS},
                                {value: dataItem.REMARKS},
                                {value: dataItem.RECOMMENDED_REMARKS},
                                {value: dataItem.RECOMMENDED_DATE},
                                {value: dataItem.APPROVED_REMARKS},
                                {value: dataItem.APPROVED_DATE}
                            ]
                        });
                        totalHours = totalHours + parseFloat(dataItem.TOTAL_HOUR);
                    }
                    if (e.target.id === "exportCalculated") {
                        rows.push({
                            cells: [
                                {value: ""},
                                {value: ""},
                                {value: ""},
                                {value: ""},
                                {value: ""},
                                {value: ""},
                                {value: totalHours},
                                {value: ""},
                                {value: ""},
                                {value: ""},
                                {value: ""},
                                {value: ""},
                                {value: ""},
                                {value: ""},
                                {value: ""},
                                {value: ""}
                            ]
                        });
                    }
                    excelExport(rows);
                    e.preventDefault();
                };

                $("#export").click(function (e) {
                    exportAction(e);
                });
                $('#exportCalculated').click(function (e) {
                    exportAction(e);
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
                                title: "Overtime Request",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "OvertimeRequestList.xlsx"});
                }

                window.app.UIConfirmations();
            };
        });