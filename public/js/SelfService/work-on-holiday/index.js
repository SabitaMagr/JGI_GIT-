(function ($, app) {
    'use strict';
    $(document).ready(function () {


        var $table = $('#workOnHolidayTbl');
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
            {field: "HOLIDAY_ENAME", title: "Holiday Name"},
            {title: "Applied Date",
                columns: [{
                        field: "REQUESTED_DATE_AD",
                        title: "AD",
                        template: "<span> #: (REQUESTED_DATE_AD == null) ? '-' : REQUESTED_DATE_AD #</span>"},
                    {field: "REQUESTED_DATE_BS",
                        title: "BS",
                        template: "<span> #: (REQUESTED_DATE_BS == null) ? '-' : REQUESTED_DATE_BS #</span>"
                    }]},
            {title: "From Date",
                columns: [{
                        field: "FROM_DATE_AD",
                        title: "AD",
                        template: "<span> #: (FROM_DATE_AD == null) ? '-' : FROM_DATE_AD #</span>"},
                    {field: "FROM_DATE_BS",
                        title: "BS",
                        template: "<span>#: (FROM_DATE_BS == null) ? '-' : FROM_DATE_BS #</span>"}
                ]},
            {title: "To Date",
                columns: [{
                        field: "TO_DATE_AD",
                        title: "AD",
                        template: "<span>#: (TO_DATE_AD == null) ? '-' : TO_DATE_AD #</span>"},
                    {field: "TO_DATE_BS",
                        title: "BS",
                        template: "<span>#: (TO_DATE_BS == null) ? '-' : TO_DATE_BS #</span>"}
                ]},
            {field: "DURATION", title: "Duration"},
            {field: "STATUS", title: "Status"},
            {field: ["ID", "ALLOW_TO_EDIT"], title: "Action", template: action}
        ], "workOnHoliday List.xlsx");


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });

        app.searchTable('workOnHolidayTbl', ['HOLIDAY_ENAME', 'REQUESTED_DATE_AD', 'REQUESTED_DATE_BS', 'FROM_DATE_AD', 'FROM_DATE_BS', 'TO_DATE_AD', 'TO_DATE_BS', 'DURATION', 'STATUS']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'HOLIDAY_ENAME': 'Holiday',
                'REQUESTED_DATE_AD': 'Requested Date(AD)',
                'REQUESTED_DATE_BS': 'Requested Date(BS)',
                'FROM_DATE_AD': 'From Date(AD)',
                'FROM_DATE_BS': 'From Date(BS)',
                'TO_DATE_AD': 'To Date(AD)',
                'TO_DATE_BS': 'To Date(BS)',
                'DURATION': 'Duration',
                'STATUS': 'Status',
                'REMARKS': 'Remarks',
                'RECOMMENDER_NAME': 'Recommender',
                'APPROVER_NAME': 'Approver',
                'RECOMMENDED_REMARKS': 'Recommended Remarks',
                'RECOMMENDED_DATE': 'Recommended Dt',
                'APPROVED_REMARKS': 'Approved Remarks',
                'APPROVED_DATE': 'Approved Dt'
            }, 'workOnHoliday List');
        });
        
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'HOLIDAY_ENAME': 'Holiday',
                'REQUESTED_DATE_AD': 'Requested Date(AD)',
                'REQUESTED_DATE_BS': 'Requested Date(BS)',
                'FROM_DATE_AD': 'From Date(AD)',
                'FROM_DATE_BS': 'From Date(BS)',
                'TO_DATE_AD': 'To Date(AD)',
                'TO_DATE_BS': 'To Date(BS)',
                'DURATION': 'Duration',
                'STATUS': 'Status',
                'REMARKS': 'Remarks',
                'RECOMMENDER_NAME': 'Recommender',
                'APPROVER_NAME': 'Approver',
                'RECOMMENDED_REMARKS': 'Recommended Remarks',
                'RECOMMENDED_DATE': 'Recommended Dt',
                'APPROVED_REMARKS': 'Approved Remarks',
                'APPROVED_DATE': 'Approved Dt'
            }, 'workOnHoliday List');
        });


    });
})(window.jQuery, window.app);