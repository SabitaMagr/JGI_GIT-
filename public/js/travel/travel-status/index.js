(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        var $table = $('#travelRequestStatusTable');
        var $travelStatus = $('#travelRequestStatusId');
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate');
        var $search = $('#search');
        app.initializeKendoGrid($table, [
            {field: "FULL_NAME", title: "Employee"},
            {title: "From Date",
                columns: [{
                        field: "FROM_DATE",
                        title: "AD",
                        template: "<span>#: (FROM_DATE == null) ? '-' : FROM_DATE #</span>"},
                    {field: "FROM_DATE_N",
                        title: "BS",
                        template: "<span>#: (FROM_DATE_N == null) ? '-' : FROM_DATE_N #</span>"}]},
            {title: "To Date",
                columns: [{
                        field: "TO_DATE",
                        title: "AD",
                        template: "<span>#: (TO_DATE == null) ? '-' : TO_DATE #</span>"},
                    {field: "TO_DATE_N",
                        title: "BS",
                        template: "<span>#: (TO_DATE_N == null) ? '-' : TO_DATE_N #</span>"}]},
            {title: "Requested Date",
                columns: [{
                        field: "REQUESTED_DATE",
                        title: "AD",
                        template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                    {field: "REQUESTED_DATE_N",
                        title: "BS",
                        template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}]},
            {field: "DESTINATION", title: "Destination"},
            {field: "REQUESTED_AMOUNT", title: "Requested Amt."},
            {field: "REQUESTED_TYPE", title: "Request For"},
            {field: "STATUS", title: "Status"},
            {field: ["TRAVEL_ID", "REQUESTED_TYPE"], title: "Action",
                template: `
                <span>
                    #if(REQUESTED_TYPE=='Expense'){ #
                    <a class="btn-edit"
                       href="${document.expenseDetailLink}/#: TRAVEL_ID #" style="height:17px;" title="view detail">
                       <i class="fa fa-search-plus"></i>
                    </a> #} else{ # <a class="btn-edit"
                                       href="${document.viewLink}/#: TRAVEL_ID #" style="height:17px;" title="view detail">
                        <i class="fa fa-search-plus"></i>
                    </a>
                    # }# </span>
`}
        ], 'TravelRequestList.xlsx');
        app.searchTable('travelRequestStatusTable', ['FULL_NAME', 'FROM_DATE', 'TO_DATE', 'REQUESTED_DATE', 'FROM_DATE_N', 'TO_DATE_N', 'REQUESTED_DATE_N', 'DESTINATION', 'REQUESTED_AMOUNT', 'REQUESTED_TYPE', 'STATUS']);
        var map = {
            'FULL_NAME': 'Name',
            'FROM_DATE': 'From Date(AD)',
            'FROM_DATE_N': 'From Date(BS)',
            'TO_DATE': 'To Date(AD)',
            'TO_DATE_N': 'To Date(BS)',
            'REQUESTED_DATE': 'Request Date(AD)',
            'REQUESTED_DATE_N': 'Request Date(BS)',
            'REQUESTED_AMOUNT': 'Request Amt',
            'REQUESTED_TYPE': 'Request Type',
            'DESTINATION': 'Destination',
            'PURPOSE': 'Purpose',
            'RECOMMENDER_NAME': 'Recommender',
            'APPROVER_NAME': 'Approver',
            'STATUS': 'Status',
            'REMARKS': 'Remarks',
            'RECOMMENDED_REMARKS': 'Recommender Remarks',
            'RECOMMENDED_DATE': 'Recommended Date',
            'APPROVED_REMARKS': 'Approver Remarks',
            'APPROVED_DATE': 'Approved Date',
        };
        $('#exportExcel').on('click', function () {
            app.excelExport($table, map, "TravelRequestList.xlsx");
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, "TravelRequestList.pdf");
        });
        $search.on('click', function () {
            var data = document.searchManager.getSearchValues();
            data['travelRequestStatusId'] = $travelStatus.val();
            data['fromDate'] = $fromDate.val();
            data['toDate'] = $toDate.val();
            app.pullDataById(document.url, {action: 'pullTravelRequestStatusList', data: data}).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });
    });
})(window.jQuery, window.app);