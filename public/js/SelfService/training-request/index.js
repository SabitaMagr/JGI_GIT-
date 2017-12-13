(function ($, app) {
    'use strict';
    $(document).ready(function () {

        var $table = $('#trainingRequestTable');
        var viewAction = '<span><a class="btn-edit" href="' + document.viewLink + '/#: REQUEST_ID #" style="height:17px;" title="view detail">'
                + '<i class="fa fa-search-plus"></i>'
                + '</a>';
        var deleteAction = '#if(ALLOW_TO_EDIT == 1){#'
                + '<a class="confirmation btn-delete" href="' + document.deleteLink + '/#: REQUEST_ID #" id="bs_#:REQUEST_ID #" style="height:17px;">'
                + '<i class="fa fa-trash-o"></i>'
                + '</a> #}#'
                + '</span>';
        var action = viewAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "TITLE", title: "Training Name"},
            {title: "Applied Date",
                columns: [{
                        field: "REQUESTED_DATE_AD",
                        title: "AD",
                        template: "<span>#: (REQUESTED_DATE_AD == null) ? '-' : REQUESTED_DATE_AD #</span>"},
                    {field: "REQUESTED_DATE_BS",
                        title: "BS",
                        template: "<span>#: (REQUESTED_DATE_BS == null) ? '-' : REQUESTED_DATE_BS #</span>"}
                ]},
            {title: "Start Date",
                columns: [{
                        field: "START_DATE_AD",
                        title: "AD",
                        template: "<span>#: (START_DATE_AD == null) ? '-' : START_DATE_AD #</span>"},
                    {field: "START_DATE_BS",
                        title: "BS",
                        template: "<span>#: (START_DATE_BS == null) ? '-' : START_DATE_BS #</span>"}
                ]},
            {title: "End Date",
                columns: [{
                        field: "END_DATE_AD",
                        title: "AD",
                        template: "<span>#: (END_DATE_AD == null) ? '-' : END_DATE_AD #</span>"},
                    {field: "END_DATE_BS",
                        title: "BS",
                        template: "<span>#: (END_DATE_BS == null) ? '-' : END_DATE_BS #</span>"}
                ]},
            {field: "DURATION", title: "Duration"},
            {field: "TRAINING_TYPE", title: "Training Type"},
            {field: "STATUS", title: "Status"},
            {field: ["REQUEST_ID", "ALLOW_TO_EDIT"], title: "Action", template: action}
        ]);

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });


        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'TRAINING_CODE': 'Training',
                'TITLE': 'Title',
                'REQUESTED_DATE_AD': 'Requested Date(AD',
                'REQUESTED_DATE_BS': 'Requested Date(BS)',
                'START_DATE_AD': 'Start Date(AD)',
                'START_DATE_BS': 'Start Date(BS)',
                'END_DATE_AD': 'End Date(AD)',
                'END_DATE_BS': 'End Date(BS)',
                'DURATION': 'Duration',
                'TRAINING_TYPE': 'Type',
                'STATUS': 'Status',
                'DESCRIPTION': 'Description',
                'REMARKS': 'Remarks',
                'RECOMMENDER_NAME': 'Recommeder',
                'APPROVER_NAME': 'Approver',
                'RECOMMENDED_REMARKS': 'Recommeder Remarks',
                'RECOMMENDED_DATE': 'Recommeder Date',
                'APPROVED_REMARKS': 'Approved Remarks',
                'APPROVED_DATE': 'Approved Dt'
            }, 'Training Request List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'TRAINING_CODE': 'Training',
                'TITLE': 'Title',
                'REQUESTED_DATE_AD': 'Requested Date(AD',
                'REQUESTED_DATE_BS': 'Requested Date(BS)',
                'START_DATE_AD': 'Start Date(AD)',
                'START_DATE_BS': 'Start Date(BS)',
                'END_DATE_AD': 'End Date(AD)',
                'END_DATE_BS': 'End Date(BS)',
                'DURATION': 'Duration',
                'TRAINING_TYPE': 'Type',
                'STATUS': 'Status',
                'DESCRIPTION': 'Description',
                'REMARKS': 'Remarks',
                'RECOMMENDER_NAME': 'Recommeder',
                'APPROVER_NAME': 'Approver',
                'RECOMMENDED_REMARKS': 'Recommeder Remarks',
                'RECOMMENDED_DATE': 'Recommeder Date',
                'APPROVED_REMARKS': 'Approved Remarks',
                'APPROVED_DATE': 'Approved Dt'
            }, 'Training Request List', 'A2');
        });

        app.searchTable('trainingRequestTable', ['TITLE', 'REQUESTED_DATE_AD', 'REQUESTED_DATE_BS', 'START_DATE_AD', 'START_DATE_BS', 'END_DATE_AD', 'END_DATE_BS', 'DURATION', 'TRAINING_TYPE', 'STATUS']);


    });
})(window.jQuery, window.app);