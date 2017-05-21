(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller("attendanceListController", function ($scope, $http) {
            var $tableContainer = $("#attendanceByHrTable");
            var displayKendo = false;
            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
                var fromDate = angular.element(document.getElementById('fromDate')).val();
                var toDate = angular.element(document.getElementById('toDate')).val();
                var status = angular.element(document.getElementById('statusId')).val();
                var missPunchOnly = 0;
                if (($("#missPunchOnly").is(":checked"))) {
                    missPunchOnly = 1;
                }
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullAttendanceList',
                    data: {
                        'employeeId': employeeId,
                        'companyId': companyId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'fromDate': fromDate,
                        'toDate': toDate,
                        'status': status,
                        'missPunchOnly': missPunchOnly
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    console.log(success.data);
                    $scope.$apply(function () {
//                       initializekendoGrid();
                        var dataSource = new kendo.data.DataSource({data: success.data});
                        var grid = $('#attendanceByHrTable').data("kendoGrid");
                        dataSource.read();
                        grid.setDataSource(dataSource);
//                       $scope.initializekendoGrid(success.data); 
//                        $('#attendanceByHrTable').data('kendoGrid').dataSource.read();
//                        $('#attendanceByHrTable').data('kendoGrid').refresh();
                    });

                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            };
            var firstTime = true;
//             function initializekendoGrid () {
                $("#attendanceByHrTable").kendoGrid({
                excel: {
                    fileName: "AttendanceList.xlsx",
                    filterable: true,
                    allPages: true
                },
//                    dataSource: {
//                        data: attendanceList,
//                        pageSize: 20,
//                        read: {
//                            cache: false
//                        }
//                    },
                height: 450,
                scrollable: true,
                sortable: true,
                filterable: true,
                serverPaging: true,
                serverSorting: true,
                pageable: {
                    input: true,
                    numeric: false,
                    refresh: true
                },
                dataBound: gridDataBound,
//                    rowTemplate: kendo.template($("#rowTemplate").html()),
                columns: [
                    {field: "EMPLOYEE_NAME", title: "Employee", width: 160},
                    {field: "ATTENDANCE_DT", title: "Attendance Date", width: 120},
                    {field: "IN_TIME", title: "Check In", width: 110},
                    {field: "OUT_TIME", title: "Check Out", width: 120},
                    {field: "STATUS", title: "Status", width: 150},
                ],
                detailInit: function detailInit(e) {
                    var dataSource = $("#attendanceByHrTable").data("kendoGrid").dataSource.data();
                    console.log(dataSource);
                    console.log(e.data.ID);
                    var parentId = e.data.ID;
                    var childData = $.grep(dataSource, function (e) {
                        return e.ID === parentId;
                    });
                    console.log(childData)
                    if (firstTime) {
                        App.blockUI({target: "#hris-page-content"});

                    } else {
                        App.blockUI({target: "#attendanceByHrTable"});
                    }
                    window.app.pullDataById(document.url, {
                        action: 'pullInOutTime',
                        data: {
                            employeeId: e.data.EMPLOYEE_ID,
                            attendanceDt: e.data.ATTENDANCE_DT
                        },
                    }).then(function (success) {
                        if (firstTime) {
                            App.unblockUI("#hris-page-content");
                            firstTime = false;
                        } else {
                            App.unblockUI("#attendanceByHrTable");
                        }
                        console.log(success.data);
                        if (success.data.length > 0) {
                            inOutTimeList = success.data;
                        } else {
                            inOutTimeList = childData;
                        }
                        $("<div/>", {
                            class: "col-sm-3",
                            css: {
                                float: "left",
                                padding: "0px",
                            }
                        }).appendTo(e.detailCell).kendoGrid({
                            dataSource: {
                                data: inOutTimeList,
                                pageSize: 10,
                                read: {
                                    cache: false
                                }
                            },
                            scrollable: false,
                            sortable: false,
                            pageable: false,
                            serverPaging: true,
                            serverSorting: true,
                            serverFiltering: true,
                            columns:
                                    [
                                        {field: "IN_TIME", title: "In Time"},
                                        {field: "OUT_TIME", title: "Out Out"},
                                    ]
                        }).data("kendoGrid");
                        $("<div/>", {
                            class: "col-sm-8",
                            css: {
                                float: "left",
                                padding: "0px",
                                margin: "0px 0px 0px 20px"
                            }
                        }).appendTo(e.detailCell).kendoGrid({
                            dataSource: {
                                data: childData,
                                pageSize: 5,
                                read: {
                                    cache: false
                                }
                            },
                            scrollable: false,
                            sortable: false,
                            pageable: false,
                            serverPaging: true,
                            serverSorting: true,
                            serverFiltering: true,
                            columns:
                                    [
                                        {field: "IN_REMARKS", title: "In Remarks"},
                                        {field: "OUT_REMARKS", title: "Out Remarks"},
                                    ]
                        }).data("kendoGrid");
                    }, function (failure) {
                        if (firstTime) {
                            App.unblockUI("#hris-page-content");
                            firstTime = false;
                        } else {
                            App.unblockUI("#attendanceByHrTable");
                        }
                        console.log(failure);
                    });
                },
            });

//            };
            
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
                            {value: "Attendance Date"},
                            {value: "Check In Time"},
                            {value: "Check Out Time"},
                            {value: "Late In Reason"},
                            {value: "Late Out Reason"},
                            {value: "Total Hour"},
                            {value: "Status"}
                        ]
                    }];
                var dataSource = $("#attendanceByHrTable").data("kendoGrid").dataSource;
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
                            {value: dataItem.ATTENDANCE_DT},
                            {value: dataItem.IN_TIME},
                            {value: dataItem.OUT_TIME},
                            {value: dataItem.IN_REMARKS},
                            {value: dataItem.OUT_REMARKS},
                            {value: dataItem.TOTAL_HOUR},
                            {value: dataItem.STATUS}
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
                            title: "Attendance Report",
                            rows: rows
                        }
                    ]
                });
                kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AttendanceList.xlsx"});
            }

            window.app.UIConfirmations();

        });
