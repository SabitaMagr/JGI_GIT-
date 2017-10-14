(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        app.getServerDate().then(function (response) {
            $('#fromDate').val(response.data.serverDate);
            $('#nepaliFromDate').val(nepaliDatePickerExt.fromEnglishToNepali(response.data.serverDate));
        });
    });
})(window.jQuery, window.app);
angular.module('hris', [])
        .controller("attendanceListController", function ($scope, $http) {
            var $grid = $("#attendanceByHrTable");
            var $employeeId = angular.element(document.getElementById('employeeId'));
            var $companyId = angular.element(document.getElementById('companyId'));
            var $branchId = angular.element(document.getElementById('branchId'));
            var $departmentId = angular.element(document.getElementById('departmentId'));
            var $designationId = angular.element(document.getElementById('designationId'));
            var $positionId = angular.element(document.getElementById('positionId'));
            var $serviceTypeId = angular.element(document.getElementById('serviceTypeId'));
            var $serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId'));
            var $employeeTypeId = angular.element(document.getElementById('employeeTypeId'));
            var $fromDate = angular.element(document.getElementById('fromDate'));
            var $toDate = angular.element(document.getElementById('toDate'));
            var $status = angular.element(document.getElementById('statusId'));
            var $missPunchOnly = $("#missPunchOnly");
            var firstTime = true;

            var checkedIds = [];
            $scope.view = function () {
                var data = {};
                data['employeeId'] = $employeeId.val();
                data['companyId'] = $companyId.val();
                data['branchId'] = $branchId.val();
                data['departmentId'] = $departmentId.val();
                data['designationId'] = $designationId.val();
                data['positionId'] = $positionId.val();
                data['serviceTypeId'] = $serviceTypeId.val();
                data['serviceEventTypeId'] = $serviceEventTypeId.val();
                data['employeeTypeId'] = $employeeTypeId.val();
                data['fromDate'] = $fromDate.val();
                data['toDate'] = $toDate.val();
                data['status'] = $status.val();
                data['missPunchOnly'] = $missPunchOnly.is(":checked") ? 1 : 0;
                window.app.pullDataById(document.pullAttendanceWS, data).then(function (response) {
                    if (response.success) {
                        window.app.renderKendoGrid($grid, response.data);
                    } else {
                        window.app.showMessage(response.error, 'error');
                    }
                }, function (error) {
                    window.app.showMessage(error, 'error');
                });
            };
            var columns = [
                {
                    title: 'Select All',
                    headerTemplate: "<input type='checkbox' id='header-chb' class='k-checkbox header-checkbox'><label class='k-checkbox-label' for='header-chb'></label>",
                    template: "<input type='checkbox' id='#:ID#'  class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:ID#'></label>",
                    width: 80
                },
                {field: "EMPLOYEE_NAME", title: "Employee", template: "<span>#: (EMPLOYEE_NAME == null) ? '-' : EMPLOYEE_NAME # </span>"},
                {title: "Attendance Date",
                    columns: [
                        {field: "ATTENDANCE_DT",
                            title: "AD",
                            template: "<span>#: (ATTENDANCE_DT == null) ? '-' : ATTENDANCE_DT # </span>"},
                        {field: "ATTENDANCE_DT_N",
                            title: "BS",
                            template: "<span>#: (ATTENDANCE_DT_N == null) ? '-' : ATTENDANCE_DT_N # </span>"}
                    ]},
                {field: "IN_TIME", title: "Check In", template: "<span>#: (IN_TIME == null) ? '-' : IN_TIME # </span>"},
                {field: "OUT_TIME", title: "Check Out", template: "<span>#: (OUT_TIME == null) ? '-' : OUT_TIME # </span>"},
                {field: "STATUS", title: "Status", template: "<span>#: (STATUS == null) ? '-' : STATUS # </span>"},
            ];
            var exportMap = {
                'EMPLOYEE_NAME': ' Name',
                'ATTENDANCE_DT': 'Attendance Date(AD)',
                'ATTENDANCE_DT_N': 'Attendance Date(BS)',
                'IN_TIME': 'In Time',
                'OUT_TIME': 'Out Time',
                'IN_REMARKS': 'In Remarks',
                'OUT_REMARKS': 'Out Remarks',
                'TOTAL_HOUR': 'Total Hour',
                'STATUS': 'Status'
            };
            window.app.initializeKendoGrid($grid, columns, "AttendanceList.xlsx", detailInit);
            window.app.searchTable("attendanceByHrTable", ['EMPLOYEE_NAME']);
            $('#excelExport').on('click', function () {
                console.log("test");
                window.app.excelExport($grid, exportMap, "AttendanceList.xlsx");
            });
            $('#pdfExport').on('click', function () {
                window.app.exportToPDF($grid, exportMap, "AttendanceList.pdf");

            });
            function detailInit(e) {
                var dataSource = $("#attendanceByHrTable").data("kendoGrid").dataSource.data();
                var parentId = e.data.ID;
                var childData = $.grep(dataSource, function (e) {
                    return e.ID === parentId;
                });
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.url, {
                    action: 'pullInOutTime',
                    data: {
                        employeeId: e.data.EMPLOYEE_ID,
                        attendanceDt: e.data.ATTENDANCE_DT
                    },
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
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
                                        template: "<img class='img-thumbnail' style='height:35px;width:40px;' src='" + document.picUrl + "' id=''/>",
                                        field: "IN_REMARKS", title: "Attendance Photo"
                                    },
                                ]
                    }).data("kendoGrid");
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }


            function selectRow() {
                var checked = this.checked,
                        row = $(this).closest("tr"),
                        grid = $grid.data("kendoGrid"),
                        dataItem = grid.dataItem(row);
                var index = null;
                for (var i = 0; i < checkedIds.length; i++) {
                    if (checkedIds[i].EMPLOYEE_ID == dataItem.EMPLOYEE_ID && checkedIds[i].ATTENDANCE_DT == dataItem.ATTENDANCE_DT) {
                        index = i;
                        break;
                    }
                }

                if (index === null) {
                    checkedIds.push({EMPLOYEE_ID: dataItem.EMPLOYEE_ID, ATTENDANCE_DT: dataItem.ATTENDANCE_DT});
                } else {
                    if (!checked) {
                        checkedIds.splice(index, 1);
                    }
                }

                if (checked) {
                    //-select the row
                    row.addClass("k-state-selected");
                } else {
                    //-remove selection
                    row.removeClass("k-state-selected");
                }

                var checkedNo = $('.k-state-selected').length;
                if (checkedNo > 0) {
                    $('#acceptRejectDiv').show();
                    if ($('#header-chb').prop('checked') == 1 && checkedNo == 1) {
                        $('#acceptRejectDiv').hide();
                    }
                } else {
                    $('#acceptRejectDiv').hide();
                }
            }
            $grid.on("click", ".k-checkbox", selectRow);
            $('#header-chb').change(function (ev) {
                var checked = ev.target.checked;
                $('.row-checkbox').each(function (idx, item) {
                    if (checked) {
                        if (!($(item).closest('tr').is('.k-state-selected'))) {
                            $(item).click();
                        }
                    } else {
                        if ($(item).closest('tr').is('.k-state-selected')) {
                            $(item).click();
                        }
                    }
                });
            });
            $(".btnApproveReject").bind("click", function () {
                var btnId = $(this).attr('id');
                App.blockUI({target: "#hris-page-content"});
                app.pullDataById(
                        document.bulkAttendanceWS,
                        {data: checkedIds, action: btnId}
                ).then(function (response) {
                    App.unblockUI("#hris-page-content");
                    if (response.success) {
                        $scope.$apply(function () {
                            $scope.view();
                        });
                    }
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                });
            });
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
                    if (idFromParameter == 7 || idFromParameter == 6) {
                        $fromDate.val(yesterdayDate);
                        $toDate.val(yesterdayDate);
                    } else {
                        $fromDate.val(todayDate);
                        $toDate.val(todayDate);
                    }
                }
                $scope.view();
            }
            console.log();
        });

