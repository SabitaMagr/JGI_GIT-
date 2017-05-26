(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate');
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller('jobHistoryController', function ($scope, $http) {
            $scope.jobHistoryList = [];
            var displayKendoFirstTime = true;
            var $tableContainer = $("#jobHistoryTable");
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId1')).val();
                var fromDate = angular.element(document.getElementById('fromDate')).val();
                var toDate = angular.element(document.getElementById('toDate')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.restfulUrl, {
                    action: 'pullJobHistoryList',
                    data: {
                        'fromDate': fromDate,
                        'toDate': toDate,
                        'employeeId': employeeId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'companyId': companyId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    console.log(success);
                    if (displayKendoFirstTime) {
                        $scope.initializekendoGrid();
                        displayKendoFirstTime = false;
                    }
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#jobHistoryTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                    window.app.scrollTo('jobHistoryTable');
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }

            $scope.initializekendoGrid = function () {
                $("#jobHistoryTable").kendoGrid({
                    excel: {
                        fileName: "JobHistoryList.xlsx",
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
                        {field: "FIRST_NAME", title: "Employee Name", width: 200},
                        {field: "START_DATE", title: "Start Date", width: 120},
                        {field: "SERVICE_EVENT_TYPE_NAME", title: "Service Event Type", width: 150},
                        {field: "FROM_SERVICE_NAME", title: "Service Type", width: 150},
                        {field: "FROM_BRANCH_NAME", title: "Branch", width: 150},
                        {field: "FROM_DEPARTMENT_NAME", title: "Department", width: 150},
                        {field: "FROM_DESIGNATION_TITLE", title: "Designation", width: 150},
                        {field: "FROM_POSITION_NAME", title: "Position", width: 150},
                        {title: "Action", width: 140}
                    ]
                });
                
                app.searchTable('jobHistoryTable',['FIRST_NAME','START_DATE','SERVICE_EVENT_TYPE_NAME','FROM_SERVICE_NAME','FROM_BRANCH_NAME','FROM_DEPARTMENT_NAME','FROM_DESIGNATION_TITLE','FROM_POSITION_NAME']);
                
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
                                {value: "Start Date"},
                                {value: "Service Event Type"},
                                {value: "Service Type"},
                                {value: "Branch"},
                                {value: "Department"},
                                {value: "Designation"},
                                {value: "Position"}
                            ]
                        }];
                    var dataSource = $("#jobHistoryTable").data("kendoGrid").dataSource;
                    var filteredDataSource = new kendo.data.DataSource({
                        data: dataSource.data(),
                        filter: dataSource.filter()
                    });

                    filteredDataSource.read();
                    var data = filteredDataSource.view();

                    for (var i = 0; i < data.length; i++) {
                        var dataItem = data[i];
                        var middleName = dataItem.MIDDLE_NAME != null ? " " + dataItem.MIDDLE_NAME + " " : " ";
                        rows.push({
                            cells: [
                                {value: dataItem.FIRST_NAME + middleName + dataItem.LAST_NAME},
                                {value: dataItem.START_DATE},
                                {value: dataItem.SERVICE_EVENT_TYPE_NAME},
                                {value: dataItem.FROM_SERVICE_NAME + "-" + dataItem.TO_SERVICE_NAME},
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
                window.app.UIConfirmations();
            }
        });
