(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller('recommedApproverController', function ($scope, $http, $window) {
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
                var recommenderId = angular.element(document.getElementById('recommenderId')).val();
                var approverId = angular.element(document.getElementById('approverId')).val();

                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullEmployeeForRecomApproverAssign',
                    data: {
                        'employeeId': employeeId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'companyId': companyId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'recommenderId': recommenderId,
                        'approverId': approverId
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    var tempData = success.data;
                    var num =success.data.length;
                    console.log(num);
                    var dataArray = tempData.filter(function(obj) {
                        var RECOMMENDER_ID = obj.RECOMMENDER_ID;
                        var APPROVER_ID = obj.APPROVER_ID;
                        return !(RECOMMENDER_ID===null && APPROVER_ID===null);
                    });
                    console.log("pullEmployeeList", dataArray);
                    $scope.initializekendoGrid(dataArray);
                    window.app.scrollTo('recommendApproveTable');
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            };
            $scope.initializekendoGrid = function (employees) {
                $("#recommendApproveTable").kendoGrid({
                    dataSource: {
                        data: employees,
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
                    rowTemplate: kendo.template($("#rowTemplate").html()),
                    columns: [
                        {field: "FIRST_NAME", title: "Employee", width: 200},
                        {field: "FIRST_NAME_R", title: "Recommender", width: 200},
                        {field: "FIRST_NAME_A", title: "Approver", width: 200}
                    ]
                });
                $("#export").click(function (e) {
                    var rows = [{
                            cells: [
                                {value: "Employee Name"},
                                {value: "Recommender Name"},
                                {value: "Approver Name"}
                            ]
                        }];
                    var dataSource = $("#recommendApproveTable").data("kendoGrid").dataSource;
                    var filteredDataSource = new kendo.data.DataSource({
                        data: dataSource.data(),
                        filter: dataSource.filter()
                    });

                    filteredDataSource.read();
                    var data = filteredDataSource.view();

                    for (var i = 0; i < data.length; i++) {
                        var dataItem = data[i];
                        var middleName = dataItem.MIDDLE_NAME !== null ? " " + dataItem.MIDDLE_NAME + " " : " ";
                        var middleNameR = dataItem.MIDDLE_NAME_R !== null ? " " + dataItem.MIDDLE_NAME_R + " " : " ";
                        var middleNameA = dataItem.MIDDLE_NAME_A !== null ? " " + dataItem.MIDDLE_NAME_A + " " : " ";
                        rows.push({
                            cells: [
                                {value: dataItem.FIRST_NAME + middleName + dataItem.LAST_NAME},
                                {value: dataItem.FIRST_NAME_R + middleNameR + dataItem.LAST_NAME_R},
                                {value: dataItem.FIRST_NAME_A + middleNameA + dataItem.LAST_NAME_A}
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
                                    {autoWidth: true}
                                ],
                                title: "Recommender And Approver List",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "ReportingHierarchy.xlsx"});
                }
                window.app.UIConfirmations();
            };
        });