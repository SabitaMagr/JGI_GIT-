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

        var $table = $('#leaveRequestTable');
        var viewAction = '<span><a class="btn-edit" href="' + document.viewLink + '/#: ID #" style="height:17px;" title="view detail">'
                + '<i class="fa fa-search-plus"></i>'
                + '</a>';
        var deleteAction = '#if(ALLOW_TO_EDIT == 1){#'
                + '<a class="confirmation btn-delete" href="' + document.deleteLink + '/#: ID #" id="bs_#:ID #" style="height:17px;">'
                + '<i class="fa fa-trash-o"></i>'
                + '</a> #}#'
                + '</span>';
        var action = viewAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "LEAVE_ENAME", title: "Leave Name"},
            {title: "Applied Date",
                columns: [{
                        field: "REQUESTED_DATE_AD",
                        title: "AD",
                        template: "<span>#: (REQUESTED_DT_AD == null) ? '-' : REQUESTED_DT_AD #</span>"},
                    {field: "REQUESTED_DT_BS",
                        title: "BS",
                        template: "<span>#: (REQUESTED_DT_BS == null) ? '-' : REQUESTED_DT_BS #</span>"}]},

            {title: "From Date",
                columns: [{
                        field: "FROM_DATE_AD",
                        title: "AD",
                        template: "<span>#: (FROM_DATE_AD == null) ? '-' : FROM_DATE_AD #</span>"},
                    {field: "FROM_DATE_BS",
                        title: "BS",
                        template: "<span>#: (FROM_DATE_BS == null) ? '-' : FROM_DATE_BS #</span>"}]},
            {title: "To Date",
                columns: [{
                        field: "TO_DATE_AD",
                        title: "AD",
                        template: "<span>#: (TO_DATE_AD == null) ? '-' : TO_DATE_AD #</span>"},
                    {field: "TO_DATE_BS",
                        title: "BS",
                        template: "<span>#: (TO_DATE_BS == null) ? '-' : TO_DATE_BS #</span>"}]},

            {field: "NO_OF_DAYS", title: "Duration"},
            {field: "STATUS", title: "Status"},
            {field: ["ID", "ALLOW_TO_EDIT"], title: "Action", template: action}
        ], "leave Request List.xlsx");


        $('#viewLeaveRequestStatus').on('click', function () {
            var employeeId = $('#employeeId').val();
            var leaveId = $('#leaveId').val();
            var leaveRequestStatusId = $('#leaveRequestStatusId').val();
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();

            app.pullDataById(document.getLeaveRequest, {data: {
                    'employeeId': employeeId,
                    'leaveId': leaveId,
                    'leaveRequestStatusId': leaveRequestStatusId,
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


        app.searchTable('leaveRequestTable', ['LEAVE_ENAME', 'REQUESTED_DT_AD', 'REQUESTED_DT_Bs', 'FROM_DATE_AD', 'FROM_DATE_BS', 'TO_DATE_AD', 'TO_DATE_BS', 'NO_OF_DAYS', 'STATUS']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'LEAVE_ENAME': 'Leave',
                'REQUESTED_DT_AD': 'Requested Date(AD)',
                'REQUESTED_DT_BS': 'Requested Date(BS)',
                'FROM_DATE_AD': 'Start Date(AD)',
                'FROM_DATE_BS': 'Start Date(BS)',
                'TO_DATE_AD': 'End Date(AD)',
                'TO_DATE_BS': 'End Date(BS)',
                'NO_OF_DAYS': 'No Of Days',
                'STATUS': 'Status',
                'REMARKS': 'Remarks',
                'RECOMMENDER_NAME': 'Recommender',
                'RECOMMENDED_REMARKS': 'Recommender Remarks',
                'RECOMMENDED_DT': 'Recommended Date',
                'APPROVER_NAME': 'Approver',
                'APPROVED_REMARKS': 'Approver Remarks',
                'APPROVED_DT': 'Aprroved Date'
            }, 'leave Request List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'LEAVE_ENAME': 'Leave',
                'REQUESTED_DT_AD': 'Requested Date(AD)',
                'REQUESTED_DT_BS': 'Requested Date(BS)',
                'FROM_DATE_AD': 'Start Date(AD)',
                'FROM_DATE_BS': 'Start Date(BS)',
                'TO_DATE_AD': 'End Date(AD)',
                'TO_DATE_BS': 'End Date(BS)',
                'NO_OF_DAYS': 'No Of Days',
                'STATUS': 'Status',
                'REMARKS': 'Remarks',
                'RECOMMENDER_NAME': 'Recommender',
                'RECOMMENDED_REMARKS': 'Recommender Remarks',
                'RECOMMENDED_DT': 'Recommended Date',
                'APPROVER_NAME': 'Approver',
                'APPROVED_REMARKS': 'Approver Remarks',
                'APPROVED_DT': 'Aprroved Date'
            }, 'leave Request List');
        });

    });
})(window.jQuery, window.app);
