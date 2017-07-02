(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
//        $('#fromDate').datepicker('setDate', nepaliDatePickerExt.getDate('07-Jun-2017'));

    });
})(window.jQuery, window.app);
angular.module('hris', [])
        .controller("attendanceListController", function ($scope, $http) {
            var $tableContainer = $("#attendanceByHrTable");
            var firstTime = true;
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
                        if (displayKendoFirstTime) {
                            initializekendoGrid();
                            displayKendoFirstTime = false;
                        }
                        var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                        var grid = $('#attendanceByHrTable').data("kendoGrid");
                        dataSource.read();
                        grid.setDataSource(dataSource);
                    });

                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            };
            function initializekendoGrid() {
                $("#attendanceByHrTable").kendoGrid({
                    excel: {
                        fileName: "AttendanceList.xlsx",
                        filterable: true,
                        allPages: true
                    },
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
                    columns: [
                        {field: "EMPLOYEE_NAME", title: "Employee", width: 160},
                        {field: "ATTENDANCE_DT", title: "Attendance Date", width: 120},
                        {field: "IN_TIME", title: "Check In", width: 110},
                        {field: "OUT_TIME", title: "Check Out", width: 120},
                        {field: "STATUS", title: "Status", width: 150},
                    ],
                    detailInit: detailInit,
                });

                app.searchTable('attendanceByHrTable', ['EMPLOYEE_NAME', 'ATTENDANCE_DT', 'IN_TIME', 'OUT_TIME', 'STATUS']);
                app.pdfExport(
                        'attendanceByHrTable',
                        {
                            'EMPLOYEE_NAME': ' Name',
                            'ATTENDANCE_DT': 'Attendance Date',
                            'IN_TIME': 'In Time',
                            'OUT_TIME': 'Out Time',
                            'IN_REMARKS': 'In Remarks',
                            'OUT_REMARKS': 'Out Remarks',
                            'TOTAL_HOUR': 'Total Hour',
                            'STATUS': 'Status'
                        }
                );



            }
            ;

            function detailInit(e) {
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
                        class: "col-sm-6",
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
                    $("<div/>", {
                        class: "col-sm-2",
                        css: {
                            float: "left",
                            padding: "0px",
                            margin: "0px 0px 0px 20px",
                            width: "11%"
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
                                    {
                                        template: "<img class='img-thumbnail' style='height:35px;width:40px;' src='"+document.picUrl+"' id=''/>",
                                        field: "IN_REMARKS", title: "Attendance Photo"
                                    },
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
            }
            ;

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
                    rows.push({
                        cells: [
                            {value: dataItem.EMPLOYEE_NAME},
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
                                {autoWidth: true},
                                {autoWidth: true},
                                {autoWidth: true},
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

//            start to get the current Date in  DD-MON-YYY format
            var m_names = new Array("Jan", "Feb", "Mar",
                    "Apr", "May", "Jun", "Jul", "Aug", "Sep",
                    "Oct", "Nov", "Dec");

            var d = new Date();

            //to get today Date
            var curr_date = d.getDate();
            var curr_month = d.getMonth();
            var curr_year = d.getFullYear();
            var todayDate = curr_date + "-" + m_names[curr_month] + "-" + curr_year;

            //to get yesterday Date
            var yes_date = new Date(d);
            yes_date.setDate(d.getDate() - 1);
            var yesterday_date = yes_date.getDate();
            var yesterday_month = yes_date.getMonth();
            var yesterday_year = yes_date.getFullYear();
            var yesterdayDate = yesterday_date + "-" + m_names[yesterday_month] + "-" + yesterday_year;

            //End to get Current Date and YesterDay Date

            var idFromParameter = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
            if (parseInt(idFromParameter) > 0) {
                var $status = angular.element(document.getElementById('statusId'));
                var $missPunchOnly = angular.element(document.getElementById('missPunchOnly'));
                var $fromDate = angular.element(document.getElementById('fromDate'));
                var $toDate = angular.element(document.getElementById('toDate'));
                var map = {1: 'P', 2: 'L', 3: 'T', 4: 'TVL', 5: 'WOH', 6: 'LI', 7: 'EO'};
                if (idFromParameter == 8) {
                    $missPunchOnly.prop("checked", true);
                    $fromDate.val(yesterdayDate);
                    $toDate.val(yesterdayDate);
                } else {
                    $status.val(map[idFromParameter]).change();
                    if (idFromParameter == 7) {
                        $fromDate.val(yesterdayDate);
                        $toDate.val(yesterdayDate);
                    } else {
                        $fromDate.val(todayDate);
                        $toDate.val(todayDate);
                    }
                }
                $scope.view();
            }
//            console.log();
        });

