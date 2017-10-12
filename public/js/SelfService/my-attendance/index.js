/**
 * Created by punam on 10/5/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });



        var $table = $('#attendanceTable');

        app.initializeKendoGrid($table, [
            {title: "Attendance Date",
                columns: [{
                        field: "ATTENDANCE_DT_AD",
                        title: "AD",
                        template: "<span>#: (ATTENDANCE_DT_AD == null) ? '-' : ATTENDANCE_DT_AD #</span>"},
                    {field: "ATTENDANCE_DT_BS",
                        title: "BS",
                        template: "<span>#: (ATTENDANCE_DT_BS == null) ? '-' : ATTENDANCE_DT_BS #</span>"}]},
            {field: "IN_TIME", title: "Check In"},
            {field: "OUT_TIME", title: "Check Out"},
            {field: "TOTAL_HOUR", title: "Total Hour"},
            {field: "STATUS", title: "Status"},
            {field: "IN_REMARKS", title: "Late In Reason"},
            {field: "OUT_REMARKS", title: "Early Out Reason"},
        ], "Attendance List.xlsx");



        $('#myAttendance').on('click', function () {
            viewAttendance();
        });


        var viewAttendance = function () {
            console.log('sdf');
            var employeeId = $('#employeeId').val();
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            var status = $('#statusId').val();
            var missPunchOnly = 0;
            if (($("#missPunchOnly").is(":checked"))) {
                missPunchOnly = 1;
            }

            app.pullDataById(document.attendancelistUrl, {data: {
                    'fromDate': fromDate,
                    'toDate': toDate,
                    'employeeId': employeeId,
                    'status': status,
                    'missPunchOnly': missPunchOnly
                }}).then(function (response) {
                console.log(response);
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });

        }



        app.searchTable('attendanceTable', ['ATTENDANCE_DT_AD', 'ATTENDANCE_DT_BS', 'IN_TIME', 'OUT_TIME', 'TOTAL_HOUR', 'STATUS', 'IN_REMARKS', 'OUT_REMARKS']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'ATTENDANCE_DT_AD': ' Attendance Date(AD)',
                'ATTENDANCE_DT_BS': ' Attendance Date(BS)',
                'IN_TIME': 'In Time',
                'OUT_TIME': 'Out Time',
                'TOTAL_HOUR': 'Total Hour',
                'STATUS': 'Status',
                'IN_REMARKS': 'In Remarks',
                'OUT_REMARKS': 'Out Remarks'
            }, 'Attendance List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'ATTENDANCE_DT_AD': ' Attendance Date(AD)',
                'ATTENDANCE_DT_BS': ' Attendance Date(BS)',
                'IN_TIME': 'In Time',
                'OUT_TIME': 'Out Time',
                'TOTAL_HOUR': 'Total Hour',
                'STATUS': 'Status',
                'IN_REMARKS': 'In Remarks',
                'OUT_REMARKS': 'Out Remarks'
            }, 'Attendance List', 'A4');
        });

        setTimeout(function () {
            var pageUrl = window.location.href;
            var idFromParameter = pageUrl.substring(pageUrl.lastIndexOf('/') + 1);
            var fiscalYear = jQuery.parseJSON(document.fiscalYear);

            if (parseInt(idFromParameter) > 0) {
                var $status = $('#statusId');
                var $fromDate = $('#fromDate');
                var $toDate = $('#toDate');
                var $missPunchOnly = $('#missPunchOnly');
                var fiscalFromDate = fiscalYear.FROM_DATE;
                var fiscalEndDate = fiscalYear.TO_DATE;
                var map = {1: 'P', 2: 'L', 3: 'T', 4: 'TVL', 5: 'WOH', 6: 'LI', 7: 'EO'};

                $fromDate.val(fiscalFromDate);
                $toDate.val(fiscalEndDate);

                if (idFromParameter == 8) {
                    $missPunchOnly.prop("checked", true);
                } else {
                    $status.val(map[idFromParameter]).change();
                }

                viewAttendance();
            }
        }, 6000);



    });



})(window.jQuery, window.app);




//angular.module('hris', [])
//        .controller('attendanceController', function ($scope, $http) {
//            $scope.attendanceList = [];
//            var $tableContainer = $("#attendanceTable");
//            var displayKendoFirstTime = true;
//            $scope.view = function () {
//                var employeeId = angular.element(document.getElementById('employeeId')).val();
//                var fromDate = angular.element(document.getElementById('fromDate')).val();
//                var toDate = angular.element(document.getElementById('toDate')).val();
//                var status = angular.element(document.getElementById('statusId')).val();
//                var missPunchOnly = 0;
//                if (($("#missPunchOnly").is(":checked"))) {
//                    missPunchOnly = 1;
//                }
//
//                App.blockUI({target: "#hris-page-content"});
//                window.app.pullDataById(document.url, {
//                    action: 'pullAttendanceList',
//                    data: {
//                        'fromDate': fromDate,
//                        'toDate': toDate,
//                        'employeeId': employeeId,
//                        'status': status,
//                        'missPunchOnly': missPunchOnly
//                    }
//                }).then(function (success) {
//                    App.unblockUI("#hris-page-content");
//                    if (displayKendoFirstTime) {
//                        $scope.initializekendoGrid();
//                        displayKendoFirstTime = false;
//                    }
//                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
//                    var grid = $('#attendanceTable').data("kendoGrid");
//                    dataSource.read();
//                    grid.setDataSource(dataSource);
//                }, function (failure) {
//                    App.unblockUI("#hris-page-content");
//                    console.log(failure);
//                });
//            }
//
//            $scope.initializekendoGrid = function () {
//                $("#attendanceTable").kendoGrid({
//                    excel: {
//                        fileName: "AttendanceList.xlsx",
//                        filterable: true,
//                        allPages: true
//                    },
//                    height: 450,
//                    scrollable: true,
//                    sortable: true,
//                    filterable: true,
//                    pageable: {
//                        input: true,
//                        numeric: false
//                    },
//                    dataBound: gridDataBound,
////                    rowTemplate: kendo.template($("#rowTemplate").html()),
//                    columns: [
//                        {title: "Attendance Date",
//                            columns: [{
//                                    field: "ATTENDANCE_DT",
//                                    title: "English",
//                                    template: "<span>#: (ATTENDANCE_DT == null) ? '-' : ATTENDANCE_DT #</span>"},
//                                {field: "ATTENDANCE_DT_N",
//                                    title: "Nepali",
//                                    template: "<span>#: (ATTENDANCE_DT_N == null) ? '-' : ATTENDANCE_DT_N #</span>"}]},
//                        {field: "IN_TIME", title: "Check In"},
//                        {field: "OUT_TIME", title: "Check Out"},
//                        {field: "TOTAL_HOUR", title: "Total Hour"},
//                        {field: "STATUS", title: "Status"},
//                        {field: "IN_REMARKS", title: "Late In Reason"},
//                        {field: "OUT_REMARKS", title: "Early Out Reason"},
//                    ]
//                });
//
//                app.pdfExport(
//                        'attendanceTable',
//                        {
//                            'ATTENDANCE_DT': ' Attendance Date(AD)',
//                            'ATTENDANCE_DT_N': ' Attendance Date(BS)',
//                            'IN_TIME': 'In Time',
//                            'OUT_TIME': 'Out Time',
//                            'TOTAL_HOUR': 'Total Hour',
//                            'STATUS': 'Status',
//                            'IN_REMARKS': 'In Remarks',
//                            'OUT_REMARKS': 'Out Remarks'
//                        }
//                );
//                function gridDataBound(e) {
//                    var grid = e.sender;
//                    if (grid.dataSource.total() == 0) {
//                        var colCount = grid.columns.length;
//                        $(e.sender.wrapper)
//                                .find('tbody')
//                                .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
//                    }
//                }
//                ;
//                $("#export").click(function (e) {
//                    var grid = $("#attendanceTable").data("kendoGrid");
//                    grid.saveAsExcel();
//                });
//            }
//
//
//            var idFromParameter = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
//            var fiscalYear = jQuery.parseJSON(document.fiscalYear);
//            if (parseInt(idFromParameter) > 0) {
//
//                var $status = angular.element(document.getElementById('statusId'));
//                var $fromDate = angular.element(document.getElementById('fromDate'));
//                var $toDate = angular.element(document.getElementById('toDate'));
//                var $missPunchOnly = angular.element(document.getElementById('missPunchOnly'));
//                var fiscalFromDate = fiscalYear.FROM_DATE;
//                var fiscalEndDate = fiscalYear.TO_DATE;
//                var map = {1: 'P', 2: 'L', 3: 'T', 4: 'TVL', 5: 'WOH', 6: 'LI', 7: 'EO'};
//
//                $fromDate.val(fiscalFromDate);
//                $toDate.val(fiscalEndDate);
//
//                if (idFromParameter == 8) {
//                    $missPunchOnly.prop("checked", true);
//                } else {
//                    $status.val(map[idFromParameter]).change();
//                }
//                $scope.view();
//            }
//
//
//
//        });