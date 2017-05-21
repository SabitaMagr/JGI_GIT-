/**
 * Created by root on 11/3/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
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
        .controller('serviceController', function ($scope, $http) {
            var $tableContainer = $("#serviceHistoryTable");
            var displayKendoFirstTime = true;
            $scope.serviceHistory = [];
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var fromDate = angular.element(document.getElementById('fromDate')).val();
                var toDate = angular.element(document.getElementById('toDate')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullServiceHistory',
                    data: {
                        'fromDate': fromDate,
                        'toDate': toDate,
                        'employeeId': employeeId
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    if (displayKendoFirstTime) {
                        $scope.initializeKendoGrid();
                        displayKendoFirstTime = false;
                    }
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#serviceHistoryTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }
            $scope.initializeKendoGrid = function () {
                $("#serviceHistoryTable").kendoGrid({
                    excel: {
                        fileName: "ServiceHistory.xlsx",
                        filterable: true,
                        allPages: true
                    },
                    height: 450,
                    scrollable: true,
                    sortable: true,
                    filterable: true,
                    navigatable: true,
                    pageable: {
                        input: true,
                        numeric: false
                    },
                    dataBound: gridDataBound,
                    rowTemplate: kendo.template($("#rowTemplate").html()),
                    columns: [
                        {field: "START_DATE", title: "Start Date", width: 120},
                        {field: "END_DATE", title: "End Date", width: 120},
                        {field: "SERVICE_EVENT_TYPE_NAME", title: "Service Event Type", width: 180},
                        {field: "FROM_SERVICE_TYPE_NAME", title: "Service Type (From-To)", width: 220},
                        {field: "FROM_BRANCH_NAME", title: "Branch (From-To)", width: 250},
                        {field: "FROM_DEPARTMENT_NAME", title: "Department (From-To)", width: 300},
                        {field: "FROM_DESIGNATION_TITLE", title: "Designation (From-To)", width: 300},
                        {field: "FROM_POSITION_NAME", title: "Position (From-To)", width: 300},
                        {title: "Action", width: 100}
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
                                {value: "Start Date"},
                                {value: "End Date"},
                                {value: "Service Event Type"},
                                {value: "Service Type (From-To)"},
                                {value: "Branch (From-To)"},
                                {value: "Department (From-To)"},
                                {value: "Designation (From-To)"},
                                {value: "Position (From-To)"}
                            ]
                        }];
                    var dataSource = $("#serviceHistoryTable").data("kendoGrid").dataSource;
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
                                {value: dataItem.START_DATE},
                                {value: dataItem.END_DATE},
                                {value: dataItem.SERVICE_EVENT_TYPE_NAME},
                                {value: dataItem.FROM_SERVICE_TYPE_NAME + "-" + dataItem.TO_SERVICE_TYPE_NAME},
                                {value: dataItem.FROM_BRANCH_NAME + "-" + dataItem.TO_BRANCH_NAME},
                                {value: dataItem.FROM_DEPARTMENT_NAME + "-" + dataItem.TO_DEPARTMENT_NAME},
                                {value: dataItem.FROM_DESIGNATION_TITLE + "-" + dataItem.TO_DESIGNATION_TITLE},
                                {value: dataItem.FROM_POSITION_NAME + "-" + dataItem.TO_POSITION_NAME}
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
                                    {autoWidth: true}
                                ],
                                title: "Service Status History",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "ServiceStatusHistory.xlsx"});
                }
            }
        });