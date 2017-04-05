(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'startDate', 'nepaliToDate', 'endDate');
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("trainingStatusListController", function ($scope, $http) {
            var $tableContainer = $("#trainingRequestStatusTable");
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var trainingId = angular.element(document.getElementById('trainingId')).val();
                var requestStatusId = angular.element(document.getElementById('requestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate1')).val();
                var toDate = angular.element(document.getElementById('toDate1')).val();
                $tableContainer.block();
                window.app.pullDataById(document.url, {
                    action: 'pullTrainingRequestStatusList',
                    data: {
                        'employeeId': employeeId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'trainingId': trainingId,
                        'requestStatusId': requestStatusId,
                        'fromDate': fromDate,
                        'toDate': toDate
                    }
                }).then(function (success) {
                    $tableContainer.unblock();
                    console.log(success.data);
                    $scope.initializekendoGrid(success.data);
                }, function (failure) {
                    $tableContainer.unblock();
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function (trainingRequestStatus) {
                $("#trainingRequestStatusTable").kendoGrid({
                    excel: {
                        fileName: "TrainingRequestList.xlsx",
                        filterable: true,
                        allPages: true
                    },
                    dataSource: {
                        data: trainingRequestStatus,
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
                        {field: "TRAINING_NAME", title: "Training Name", width: 120},
                        {field: "REQUESTED_DATE", title: "Requested Date", width: 130},
                        {field: "FROM_DATE", title: "From Date", width: 100},
                        {field: "TO_DATE", title: "To Date", width: 100},
                        {field: "DURATION", title: "Duration", width: 80},
                        {field: "TRAINING_TYPE", title: "Training Type", width: 80},
                        {field: "RECOMMENDER_NAME", title: "Recommender", width: 120},
                        {field: "APPROVER_NAME", title: "Approver", width: 120},                        
                        {field: "STATUS", title: "Status", width: 100},
                        {title: "Action", width: 80}
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
                                {value: "Training Code"},
                                {value: "Training Name"},
                                {value: "Requested Date"},
                                {value: "From Date"},
                                {value: "To Date"},
                                {value: "Duration"},
                                {value: "Training Type"},
                                {value: "Recommender"},
                                {value: "Approver"},
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
                        var middleName = dataItem.MIDDLE_NAME != null ? " " + dataItem.MIDDLE_NAME + " " : " ";
                        var mn1 = dataItem.MN1 != null ? " " + dataItem.MN1 + " " : " ";
                        var mn2 = dataItem.MN2 != null ? " " + dataItem.MN2 + " " : " ";
                        rows.push({
                            cells: [
                                {value: dataItem.FIRST_NAME + middleName + dataItem.LAST_NAME},
                                {value: dataItem.TRAINING_CODE},
                                {value: dataItem.TRAINING_NAME},
                                {value: dataItem.REQUESTED_DATE},
                                {value: dataItem.FROM_DATE},
                                {value: dataItem.TO_DATE},
                                {value: dataItem.DURATION},
                                {value: dataItem.TRAINING_TYPE},
                                {value: dataItem.RECOMMENDER_NAME},
                                {value: dataItem.APPROVER_NAME},
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
                                    {autoWidth: true}
                                    
                                ],
                                title: "Training Request",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "TrainingRequestList.xlsx"});
                }
               
                window.app.UIConfirmations();
            };
        });