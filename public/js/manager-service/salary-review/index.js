(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("#salaryReviewTable").kendoGrid({
            excel: {
                fileName: "BranchList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.salaryDetails,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "FIRST_NAME", title: "Employee", width: 200},
                {field: "OLD_AMOUNT", title: "Old Amount", width: 120},
                {field: "NEW_AMOUNT", title: "New Amount", width: 120},
                {field: "EFFECTIVE_DATE", title: "Effective Date", width: 120},
                {field: "JOB_HISTORY_ID", title: "JOb History Id", width: 140},
                {title: "Action", width: 100}
            ],
        });
        
        app.searchTable('salaryReviewTable',['FIRST_NAME','OLD_AMOUNT','NEW_AMOUNT','EFFECTIVE_DATE']);
        
        app.pdfExport(
                'salaryReviewTable',
                {
                    'FIRST_NAME': 'Name',
                    'MIDDLE_NAME': 'MiddleName',
                    'LAST_NAME': 'LastName',
                    'OLD_AMOUNT': 'Old Amt',
                    'NEW_AMOUNT': 'New Amt',
                    'EFFECTIVE_DATE': 'Effective Date',
                    'JOB_HISTORY_ID': 'Job History',
                });
        

    });
})(window.jQuery, window.app);