(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate');
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("travelStatusListController", function ($scope, $http) {
            var $tableContainer = $("#travelRequestStatusTable");
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
                var fromDate = angular.element(document.getElementById('fromDate')).val();
                var toDate = angular.element(document.getElementById('toDate')).val();
                var recomApproveId = angular.element(document.getElementById('recomApproveId')).val();
                var travelRequestStatusId = angular.element(document.getElementById('travelRequestStatusId')).val();
                $tableContainer.block();
                window.app.pullDataById(document.url, {
                    action: 'pullTravelRequestStatusList',
                    data: {
                        'employeeId': employeeId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'fromDate': fromDate,
                        'toDate': toDate,
                        'recomApproveId': recomApproveId,
                        'travelRequestStatusId':travelRequestStatusId
                    }
                }).then(function (success) {
                    $tableContainer.unblock();
                    console.log(success.recomApproveId);
                    $scope.initializekendoGrid(success.data);
                }, function (failure) {
                    $tableContainer.unblock();
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function (travelRequestStatus) {
                console.log(travelRequestStatus);
                $("#travelRequestStatusTable").kendoGrid({
                    excel: {
                        fileName: "TravelRequestList.xlsx",
                        filterable: true,
                        allPages: true
                    },
                    dataSource: {
                        data: travelRequestStatus,
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
                        {field: "FIRST_NAME", title: "Employee Name", width: 140},
                        {field: "TRAVEL_CODE", title: "Travel Code", width: 120},
                        {field: "FROM_DATE", title: "From Date", width: 120},
                        {field: "TO_DATE", title: "To Date", width: 100},
                        {field: "REQUESTED_DATE", title: "Requested Date", width: 140},
                        {field: "DESTINATION", title: "Destination", width: 110},
                        {field: "REQUESTED_AMOUNT", title: "Requested Amt.", width: 140},
                        {field: "REQUESTED_TYPE", title: "Request For", width: 120},
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
                                {value: "Travel Code"},
                                {value: "From Date"},
                                {value: "To Date"},
                                {value: "Requested Date"},
                                {value: "Destination"},
                                {value: "Purpose"},
                                {value: "Requested Amount"},
                                {value: "Request For"},
                                {value: "Your Role"},
                                {value: "Status"},
                                {value: "Remarks By Employee"},
                                {value: "Remarks By Recommender"},
                                {value: "Recommended Date"},
                                {value: "Remarks By Approver"},
                                {value: "Approved Date"}
                            ]
                        }];
                    var dataSource = $("#travelRequestStatusTable").data("kendoGrid").dataSource;
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
                                {value: dataItem.TRAVEL_CODE},
                                {value: dataItem.FROM_DATE},
                                {value: dataItem.TO_DATE},
                                {value: dataItem.REQUESTED_DATE},
                                {value: dataItem.DESTINATION},
                                {value: dataItem.PURPOSE},
                                {value: dataItem.REQUESTED_AMOUNT},
                                {value: dataItem.REQUESTED_TYPE},
                                {value: dataItem.YOUR_ROLE},
                                {value: dataItem.STATUS},
                                {value: dataItem.REMARKS},
                                {value: dataItem.RECOMMENDED_REMARKS},
                                {value: dataItem.RECOMMENDED_DATE},
                                {value: dataItem.APPROVED_REMARKS},
                                {value: dataItem.APPROVED_DATE},
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
                                title: "Travel Request List",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "TravelRequestList.xlsx"});
                }
            };
        });