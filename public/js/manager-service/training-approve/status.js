(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("trainingStatusListController", function ($scope, $http) {
            var $tableContainer = $("#trainingRequestStatusTable");
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
                var requestStatusId = angular.element(document.getElementById('requestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate')).val();
                var toDate = angular.element(document.getElementById('toDate')).val();
                var recomApproveId = angular.element(document.getElementById('recomApproveId')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullTrainingRequestStatusList',
                    data: {
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
                    var grid = $('#trainingRequestStatusTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function () {
                $("#trainingRequestStatusTable").kendoGrid({
                    excel: {
                        fileName: "TrainingRequestList.xlsx",
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
                        {field: "TITLE", title: "Training"},
                         {title: "Requested Date",
                            columns: [{
                                    field: "REQUESTED_DATE",
                                    title: "English",
                                    template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                                {field: "REQUESTED_DATE_N",
                                    title: "Nepali",
                                    template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}]},
                          {title: "Start Date",
                            columns: [{
                                    field: "START_DATE",
                                    title: "English",
                                    template: "<span>#: (START_DATE == null) ? '-' : START_DATE #</span>"},
                                {field: "START_DATE_N",
                                    title: "Nepali",
                                    template: "<span>#: (START_DATE_N == null) ? '-' : START_DATE_N #</span>"}]},
                        {title: "End Date",
                            columns: [{
                                    field: "END_DATE",
                                    title: "English",
                                    template: "<span>#: (END_DATE == null) ? '-' : END_DATE #</span>"},
                                {field: "END_DATE_N",
                                    title: "Nepali",
                                    template: "<span>#: (END_DATE_N == null) ? '-' : END_DATE_N #</span>"}]},
                        {field: "DURATION", title: "Duration"},
                        {field: "TRAINING_TYPE", title: "Training Type"},
                        {field: "YOUR_ROLE", title: "Your Role"},
                        {field: "STATUS", title: "Status"},
                        {field: ["REQUEST_ID"], title: "Action", template: `<span> <a class="btn  btn-icon-only btn-success" href="` + document.viewLink + `/#: REQUEST_ID #/#: ROLE #" style="height:17px;" title="view">
                                 <i class="fa fa-search-plus"></i></a>
                  </span>`}]
                });

                app.searchTable('trainingRequestStatusTable', ['FULL_NAME', 'TITLE', 'REQUESTED_DATE', 'START_DATE', 'END_DATE', 'REQUESTED_DATE_N', 'START_DATE_N', 'END_DATE_N', 'DURATION', 'TRAINING_TYPE', 'YOUR_ROLE', 'STATUS']);

                app.pdfExport(
                        'trainingRequestStatusTable',
                        {
                            'FULL_NAME': 'Name',
                            'TRAINING_NAME': 'Training',
                            'REQUESTED_DATE': 'Request Date(AD)',
                            'REQUESTED_DATE_N': 'Request Date(BS)',
                            'START_DATE': 'Start Date(AD)',
                            'START_DATE_N': 'Start Date(BS)',
                            'END_DATE': 'End Date(AD)',
                            'END_DATE_N': 'End Date(BS)',
                            'DURATION': 'Duration',
                            'TRAINING_TYPE': 'Type',
                            'YOUR_ROLE': 'Role',
                            'STATUS': 'Status',
                            'DESCRIPTION': 'Description',
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
                                {value: "Training Name"},
                                {value: "Requested Date(AD)"},
                                {value: "Requested Date(BS)"},
                                {value: "Start Date(AD)"},
                                {value: "Start Date(BS)"},
                                {value: "End Date(AD)"},
                                {value: "End Date(BS)"},
                                {value: "Your Role"},
                                {value: "Duration"},
                                {value: "Training Type"},
                                {value: "Status"},
                                {value: "Description"},
                                {value: "Remarks"},
                                {value: "Remarks By Recommender"},
                                {value: "Recommended Date"},
                                {value: "Remarks By Approver"},
                                {value: "Approved Date"}
                            ]
                        }];
                    var dataSource = $("#trainingRequestStatusTable").data("kendoGrid").dataSource;
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
                                {value: dataItem.TRAINING_NAME},
                                {value: dataItem.REQUESTED_DATE},
                                {value: dataItem.REQUESTED_DATE_N},
                                {value: dataItem.START_DATE},
                                {value: dataItem.START_DATE_N},
                                {value: dataItem.END_DATE},
                                {value: dataItem.END_DATE_N},
                                {value: dataItem.YOUR_ROLE},
                                {value: dataItem.DURATION},
                                {value: dataItem.TRAINING_TYPE},
                                {value: dataItem.STATUS},
                                {value: dataItem.DESCRIPTION},
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
                                title: "Training Request List",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "TrainingRequestList.xlsx"});
                }
            };
        });