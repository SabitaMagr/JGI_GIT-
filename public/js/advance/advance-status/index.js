(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $search = $('#searchAdvance');
        var $table = $('#table');
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:ADVANCE_REQUEST_ID#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
            #if(STATUS=='AP'){#
                <a class="btn btn-icon-only green" href="${document.paymentViewLink}/#:ADVANCE_REQUEST_ID#" style="height:17px;" title="Payment Detail">
                    <i class="fa fa-money"></i>
                </a>
            #}#
            </div>
        `;

        var columns = [
            {field: "EMPLOYEE_NAME", title: "Employee"},
            {field: "ADVANCE_ENAME", title: "Advance"},
            {title: "Request Date",
                columns: [{
                        field: "REQUESTED_DATE_AD",
                        title: "English",
                    },
                    {
                        field: "REQUESTED_DATE_BS",
                        title: "Nepali",
                    }]},
            {title: "Date Of Advance",
                columns: [{
                        field: "DATE_OF_ADVANCE",
                        title: "English",
                    },
                    {field: "DATE_OF_ADVANCE",
                        title: "Nepali",
                    }]},
            {field: "REQUESTED_AMOUNT", title: "Request Amt."},
            {field: "STATUS_DETAIL", title: "Status"},
            {field: ["ADVANCE_REQUEST_ID"], title: "Action", template: action}
        ];


        app.initializeKendoGrid($table, columns, "Advance List.xlsx");

        app.searchTable($table, ['EMPLOYEE_NAME']);
        var exportMap = {
            'EMPLOYEE_NAME': 'Employee Name',
            'ADVANCE_ENAME': 'Advance',
            'REQUESTED_DATE_AD': 'Request Date(AD)',
            'REQUESTED_DATE_BS': 'Request Date(BS)',
            'DATE_OF_ADVANCE_AD': 'Advance Date(AD)',
            'DATE_OF_ADVANCE_BS': 'Advance Date(BS)',
            'STATUS_DETAIL': 'Status',
        };

        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Advance Request List.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Advance Request List.pdf');
        });



        $search.on('click', function () {

            var data = document.searchManager.getSearchValues();
            data['status'] = $('#status').val();
            data['fromDate'] = $('#fromDate').val();
            data['toDate'] = $('#toDate').val();
            App.blockUI({target: "#hris-page-content"});
            app.pullDataById("", data).then(function (response) {
                App.unblockUI("#hris-page-content");
                console.log(response.data);
                app.renderKendoGrid($table, response.data, "AdvanceRequestList.xlsx");
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });

    });
})(window.jQuery, window.app);
