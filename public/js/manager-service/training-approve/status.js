(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $tableContainer = $("#trainingRequestStatusTable");
        var $search = $('#search');

        var columns = [
            {field: "FULL_NAME", title: "Employee"},
            {field: "TITLE", title: "Training"},
            {title: "Requested Date",
                columns: [{
                        field: "REQUESTED_DATE_AD",
                        title: "AD",
                    },
                    {
                        field: "REQUESTED_DATE_BS",
                        title: "BS",
                    }]},
            {title: "Start Date",
                columns: [{
                        field: "START_DATE_AD",
                        title: "AD",
                    },
                    {
                        field: "START_DATE_BS",
                        title: "BS",
                    }]},
            {title: "End Date",
                columns: [{
                        field: "END_DATE_AD",
                        title: "AD",
                    },
                    {
                        field: "END_DATE_BS",
                        title: "BS",
                    }]},
            {field: "DURATION", title: "Duration"},
            {field: "TRAINING_TYPE", title: "Training Type"},
            {field: "YOUR_ROLE", title: "Your Role"},
            {field: "STATUS", title: "Status"},
            {field: ["REQUEST_ID", "ROLE"], title: "Action", template: `
            <span> 
                <a class="btn  btn-icon-only btn-success" href="${document.viewLink}/#: REQUEST_ID #/#: ROLE #" style="height:17px;" title="view">
                    <i class="fa fa-search-plus"></i>
                </a>
            </span>`}];
        var map = {
            'FULL_NAME': 'Name',
            'TRAINING_NAME': 'Training',
            'REQUESTED_DATE_AD': 'Request Date(AD)',
            'REQUESTED_DATE_BS': 'Request Date(BS)',
            'START_DATE_AD': 'Start Date(AD)',
            'START_DATE_BS': 'Start Date(BS)',
            'END_DATE_AD': 'End Date(AD)',
            'END_DATE_BS': 'End Date(BS)',
            'DURATION': 'Duration',
            'TRAINING_TYPE': 'Type',
            'YOUR_ROLE': 'Role',
            'STATUS': 'Status',
            'DESCRIPTION': 'Description',
            'REMARKS': 'Remarks',
            'RECOMMENDED_REMARKS': 'Recommended Remarks',
            'RECOMMENDED_DATE': 'Recommended Date',
            'APPROVED_REMARKS': 'Approved Remarks',
            'APPROVED_DATE': 'Approved Date'
        };
        app.initializeKendoGrid($tableContainer, columns, "Training Request List.xlsx");
        app.searchTable($tableContainer, ['FULL_NAME']);

        $search.on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['requestStatusId'] = $('#requestStatusId').val();
            q['fromDate'] = $('#fromDate').val();
            q['toDate'] = $('#toDate').val();
            q['recomApproveId'] = $('#recomApproveId').val();
            App.blockUI({target: "#hris-page-content"});
            app.pullDataById(document.pullTrainingRequestStatusListLink, q).then(function (success) {
                App.unblockUI("#hris-page-content");
                app.renderKendoGrid($tableContainer, success.data);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });
        $('#excelExport').on('click', function () {
            app.excelExport($tableContainer, map, "Training Request List.xlsx");
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($tableContainer, map, "Training Request List.pdf");
        });
    });
})(window.jQuery, window.app);
