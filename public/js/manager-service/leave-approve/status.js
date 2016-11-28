(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.addDatePicker(
                $("#fromDate"),
                $("#toDate")
                );
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("leaveStatusListController", function ($scope, $http) {

            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var leaveId = angular.element(document.getElementById('leaveId')).val();
                var leaveRequestStatusId = angular.element(document.getElementById('leaveRequestStatusId')).val();
                var fromDate = angular.element(document.getElementById('fromDate1')).val();
                var toDate = angular.element(document.getElementById('toDate1')).val();
                var recomApproveId  = angular.element(document.getElementById('recomApproveId')).val();

                window.app.pullDataById(document.url, {
                    action: 'pullLeaveRequestStatusList',
                    data: {
                        'employeeId': employeeId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'leaveId': leaveId,
                        'leaveRequestStatusId':leaveRequestStatusId,
                        'fromDate':fromDate,
                        'toDate':toDate,
                        'recomApproveId':recomApproveId
                    }
                }).then(function (success) {
                    console.log(success.recomApproveId);
                    $scope.initializekendoGrid(success.data);
                }, function (failure) {
                    console.log(failure);
                });
            }
            $scope.initializekendoGrid = function (leaveRequestStatus) {
                console.log(leaveRequestStatus);
                $("#leaveRequestStatusTable").kendoGrid({
                    dataSource: {
                        data: leaveRequestStatus,
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
                    dataBound:gridDataBound,
                    rowTemplate: kendo.template($("#rowTemplate").html()),
                    columns: [
                        {field: "FIRST_NAME", title: "Employee Name",width:200},
                        {field: "LEAVE_ENAME", title: "Leave Name",width:150},
                        {field: "APPLIED_DATE", title: "Requested Date",width:180},                        
                        {field: "START_DATE", title: "From Date",width:150},
                        {field: "END_DATE", title: "To Date",width:150},                        
                        {field: "NO_OF_DAYS", title: "Duration",width:120},
                        {field: "YOUR_ROLE", title: "Your Role",width:200},
                        {field: "STATUS", title: "Status",width:140},
                        {title: "Action",width:100}
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
                };
            };
        });