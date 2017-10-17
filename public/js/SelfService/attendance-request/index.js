(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);

        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });


        var $table = $('#attendanceRequestTable');
        var $search = $('#viewAttendanceRequestStatus');
        var viewAction = '<span><a class="btn-edit" href="' + document.viewLink + '/#: ID #" style="height:17px;" title="view detail">'
                + '<i class="fa fa-search-plus"></i>'
                + '</a>';
        var deleteAction = ' #if(ALLOW_TO_EDIT == 1){#'
                + '<a class="confirmation btn-delete" href="' + document.deleteLink + '/#: ID #" id="bs_#:ID #" style="height:17px;">'
                + '<i class="fa fa-trash-o"></i>'
                + '</a> #}#'
                + '</span>';
        var action = viewAction + deleteAction;

        app.initializeKendoGrid($table, [
            {title: "Applied Date",
                columns: [{
                        field: "REQUESTED_DT_AD",
                        title: "AD",
                        template: "<span>#: (REQUESTED_DT_AD == null) ? '-' : REQUESTED_DT_AD #</span>"},
                    {field: "REQUESTED_DT_BS",
                        title: "BS",
                        template: "<span>#: (REQUESTED_DT_BS == null) ? '-' : REQUESTED_DT_BS #</span>"}]},
            {title: "Attendance Date",
                columns: [{
                        field: "ATTENDANCE_DT_AD",
                        title: "AD",
                        template: "<span>#: (ATTENDANCE_DT_AD == null) ? '-' : ATTENDANCE_DT_AD #</span>"},
                    {field: "ATTENDANCE_DT_BS",
                        title: "BS",
                        template: "<span>#: (ATTENDANCE_DT_BS == null) ? '-' : ATTENDANCE_DT_BS #</span>"}]},

            {field: "IN_TIME", title: "Check In", template: "<span>#: (IN_TIME == null) ? '-' : IN_TIME #</span>"},
            {field: "OUT_TIME", title: "Check Out", template: "<span>#: (OUT_TIME == null) ? '-' : OUT_TIME #</span>"},
            {field: "A_STATUS", title: "Status", template: "<span>#: (A_STATUS == null) ? '-' : A_STATUS #</span>"},
            {field: ["ID", "ALLOW_TO_EDIT"], title: "Action", template: action}
        ], "Attendance Request List.xlsx");

        $search.on('click', function () {
            var employeeId = $('#employeeId').val();
            var attendanceRequestStatusId = $('#attendanceRequestStatusId').val();
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();

            app.pullDataById(document.pullAttendanceUrl, {data: {
                    'employeeId': employeeId,
                    'attendanceRequestStatusId': attendanceRequestStatusId,
                    'fromDate': fromDate,
                    'toDate': toDate
                }}).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });


        });

        app.searchTable('attendanceRequestTable', ['REQUESTED_DT_AD', 'REQUESTED_DT_BS', 'ATTENDANCE_DT_AD', 'ATTENDANCE_DT_BS', 'IN_TIME', 'OUT_TIME', 'A_STATUS']);


        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'REQUESTED_DT_AD': 'Request Date(AD)',
                'REQUESTED_DT_BS': 'Request Date(BS)',
                'ATTENDANCE_DT_AD': 'Attendance Date(AD)',
                'ATTENDANCE_DT_BS': 'Attendance Date(BS)',
                'IN_TIME': 'In Time',
                'OUT_TIME': 'Out Time',
                'IN_REMARKS': 'In Remarks',
                'OUT_REMARKS': 'Out Remarks',
                'TOTAL_HOUR': 'Total Hour',
                'A_STATUS': 'STATUS',
                'APPROVER_NAME': 'Approver',
                'APPROVED_REMARKS': 'Approved Remarks',
                'APPROVED_DT': 'Approved Date',
            }, 'Attendance Request List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'REQUESTED_DT_AD': 'Request Date(AD)',
                'REQUESTED_DT_BS': 'Request Date(BS)',
                'ATTENDANCE_DT_AD': 'Attendance Date(AD)',
                'ATTENDANCE_DT_BS': 'Attendance Date(BS)',
                'IN_TIME': 'In Time',
                'OUT_TIME': 'Out Time',
                'IN_REMARKS': 'In Remarks',
                'OUT_REMARKS': 'Out Remarks',
                'TOTAL_HOUR': 'Total Hour',
                'A_STATUS': 'STATUS',
                'APPROVER_NAME': 'Approver',
                'APPROVED_REMARKS': 'Approved Remarks',
                'APPROVED_DT': 'Approved Date',
            }, 'Attendance Request List');
        });

    });
})(window.jQuery, window.app);

