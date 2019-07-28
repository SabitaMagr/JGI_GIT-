(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate'); 
        var $presentStatusId = $("#presentStatusId");
        var $status = $('#statusId');
        var $table = $('#table');
        var $search = $('#search');

        $('select').select2();
        
        $.each(document.searchManager.getIds(), function (key, value) {
            $('#' + value).select2();
        });
        $presentStatusId.select2();
        $status.select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        app.getServerDate().then(function (response) {
            $fromDate.val(response.data.serverDate);
            $('#nepaliFromDate').val(nepaliDatePickerExt.fromEnglishToNepali(response.data.serverDate));
        });
        

        function loadKendo(data){
            $("#table").kendoGrid({
                dataSource: {
                    data: data,
                    pageSize: 20
                },
                toolbar: ["excel", "pdf"],
                excel: {
                    fileName: "Exported from HRIS.xlsx",
                    filterable: true,
                    allPages: true
                },
                pdf: {
                    fileName: "Exported from HRIS.pdf",
                    allPages: true,
                    paperSize: "A4",
                    margin: { top: "2cm", left: "1cm", right: "1cm", bottom: "1cm" },
                    landscape: true,
                    repeatHeaders: true,
                    template: $("#page-template").html(),
                    scale: 0.8
                },
                height: 550,
                scrollable: true,
                sortable: true,
                filterable: true,
                pageable: {
                    input: true,
                    numeric: false
                },
                columns: [
                    {field: "EMPLOYEE_CODE", title: "Code", width: '75px'},
                    {field: "FULL_NAME", title: "Employee"},
                    {field: "DEPARTMENT_NAME", title: "Department"},
                    {field: "ATTENDANCE_DT", title: "Date"},
                    {title: "Shift Time",
                        columns: [
                            {
                                field: "SHIFT_START_TIME",
                                title: "In",
                                template: "<span>#: (SHIFT_START_TIME == null) ? '-' : SHIFT_START_TIME # </span>"
                            },
                            {
                                field: "SHIFT_END_TIME",
                                title: "Out",
                                template: "<span>#: (SHIFT_END_TIME == null) ? '-' : SHIFT_START_TIME # </span>"
                            },
                            {
                                field: "TOTAL_WORKING_HR",
                                title: "Working Hour"
                            }
                        ]
                    },
                    {title: "Time",
                        columns: [
                            {
                                field: "IN_TIME",
                                title: "In",
                                template: "<span>#: (IN_TIME == null) ? '-' : IN_TIME # </span>"
                            }
                        ]
                    },
                    {title: "Break",
                        columns: [
                            {
                                field: "LUNCH_IN_TIME",
                                title: "In",
                                template: "<span>#: (LUNCH_IN_TIME == null) ? '-' : LUNCH_IN_TIME # </span>"
                            },
                            {
                                field: "LUNCH_OUT_TIME",
                                title: "Out",
                                template: "<span>#: (LUNCH_OUT_TIME == null) ? '-' : LUNCH_OUT_TIME # </span>"
                            }
                        ]
                    },
                    {title: "Time",
                        columns: [
                            {
                                field: "OUT_TIME",
                                title: "Out",
                                template: "<span>#: (OUT_TIME == null) ? '-' : OUT_TIME # </span>"
                            }
                        ]
                    },
                    {field: "ACTUAL_WORKING_HR", title: "Actual Working"},
                    {field: "OT", title: "OT Hours"}
                ]
            });
        }

        $search.on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fromDate'] = $fromDate.val();
            q['toDate'] = $toDate.val();
            q['status'] = $status.val();
            q['presentStatus'] = $presentStatusId.val();
            app.serverRequest(document.pullAttendanceWS, q).then(function (response) {
                if (response.success) {
                    loadKendo(response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        app.searchTable($table, ['EMPLOYEE_NAME', 'EMPLOYEE_CODE']);
        

        //app.excelExport($table, exportMap, "AttendanceList.xlsx");
        $("#excelExport").click(function(){    
            $("#table").table2excel({
                exclude: ".noExl",
                name: "daily-performance-report",
                filename: "daily-performance-report" 
            });
        });

        // $('#pdfExport').on('click', function () {
        //     app.exportToPDF($table, exportMap, "AttendanceList.pdf");

        // });
    });
})(window.jQuery, window.app);
