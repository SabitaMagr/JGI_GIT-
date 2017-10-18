/**
 * Created by root on 11/7/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller('leaveBalanceController', function ($scope, $http) {
            $scope.leaves = document.leaves;
            var $tableContainer = $("#leaveBalanceTable");
            var displayKendoFirstTime = true;
            $scope.allList = {};
            // console.log($scope.leaves);
            var headers = [];
            var datas = [];
            var headerForExcel = [];
            var datasForExcel = [];
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
                var employeeTypeId = angular.element(document.getElementById('employeeTypeId')).val();

                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.pullLeaveBalanceDetailLink, {
                    'employeeId': employeeId,
                    'companyId': companyId,
                    'branchId': branchId,
                    'departmentId': departmentId,
                    'designationId': designationId,
                    'positionId': positionId,
                    'serviceTypeId': serviceTypeId,
                    'serviceEventTypeId': serviceEventTypeId,
                    'employeeTypeId': employeeTypeId
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    $scope.$apply(function () {
                        $scope.allList = success.allList;
                        $scope.num = success.num;

                        initializeHeaders($scope.leaves);
                        initializeDatas($scope.leaves, $scope.allList);

                        if (displayKendoFirstTime) {
                            initializekendoGrid(headers);
                            displayKendoFirstTime = false;
                        }
                        var dataSource = new kendo.data.DataSource({data: datas, pageSize: 20});
                        var grid = $('#leaveBalanceTable').data("kendoGrid");
                        dataSource.read();
                        grid.setDataSource(dataSource);

                    });
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });

            };

            var initializeHeaders = function (cols) {
                headers = [];
                headerForExcel = [];
                headers.push({field: "EMPLOYEE_NAME", title: "Employee"});
                headerForExcel.push({value: "Employee Name"});
                for (var i in cols) {
                    headers.push({
                        field: "h" + i,
                        title: cols[i].LEAVE_ENAME,
                        template: '#if(h' + i + '.val>0 && h' + i + '.serviceEventTypeId!=8 && h' + i + '.serviceEventTypeId!=5 && h' + i + '.serviceEventTypeId!=14){# <a href="' + document.leaveApplyUrl + '/#=h' + i + '.eID#/#=h' + i + '.leaveId#">#=h' + i + '.val#</a> #}else{# #=h' + i + '.val#  #}#'
                    });
                    headerForExcel.push({value: cols[i].LEAVE_ENAME});
                }
            };

            var initializeDatas = function (cols, rows) {
                datas = [];
                datasForExcel = [];
                for (var i in rows) {
                    var temp = {};
                    var tempForExcel = [];
                    temp.EMPLOYEE_NAME = i;
                    tempForExcel.push({value: i});
                    for (var j in rows[i]) {
                        temp["h" + j  ] = {val: rows[i][j].BALANCE, eID: rows[i][j].EMPLOYEE_ID, leaveId: rows[i][j].LEAVE_ID, serviceEventTypeId: rows[i][j].SERVICE_EVENT_TYPE_ID};
                        tempForExcel.push({value: rows[i][j].BALANCE});

                    }
                    datas.push(temp);
                    datasForExcel.push(tempForExcel);
                }
            };

            var initializekendoGrid = function (columns) {
                $("#leaveBalanceTable").kendoGrid({
                    height: 450,
                    scrollable: true,
                    sortable: true,
                    filterable: true,
                    dataBound: gridDataBound,
                    pageable: {
                        input: true,
                        numeric: false
                    },
                    columns: columns
                });

                app.searchTable('leaveBalanceTable', ['EMPLOYEE_NAME']);

//                app.pdfExport(
//                'leaveBalanceTable',
//                {
//                    'EMPLOYEE_NAME': ' EMPLOYEE'
//                }
//        );

            };
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
                        cells: headerForExcel
                    }];
                for (var i in  datasForExcel) {
                    rows.push({
                        cells: datasForExcel[i]
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
                            title: "Leave Balance",
                            rows: rows
                        }
                    ]
                });
                kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "LeaveBalanceReport.xlsx"});
            }
        });