(function ($, app) {
    'use strict';
    $(document).ready(function () {
        console.log(document.loanRequestList);
        $("#loanTable").kendoGrid({
            excel: {
                fileName: "LoanRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.loanRequestList,
                pageSize: 20
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: {
                input: true,
                numeric: false
            },
            dataBound: gridDataBound,
//            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "LOAN_NAME", title: "Loan Name"},
                {title: "Applied Date",
                    columns: [{
                            field: "REQUESTED_DATE",
                            title: "English",
                            template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                        {field: "REQUESTED_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}]},
                {title: "Loan Date",
                    columns: [{
                            field: "LOAN_DATE",
                            title: "English",
                            template: "<span>#: (LOAN_DATE == null) ? '-' : LOAN_DATE #</span>"},
                        {field: "LOAN_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (LOAN_DATE_N == null) ? '-' : LOAN_DATE_N #</span>"}]},
                {field: "REQUESTED_AMOUNT", title: "Request Amount"},
                {field: "STATUS", title: "Status"},
                {field: ["LOAN_REQUEST_ID", "ALLOW_TO_EDIT"], title: "Action", template: `<span><a class="btn-edit" href="` + document.viewLink + `/#: LOAN_REQUEST_ID #" style="height:17px;" title="view detail">
        <i class="fa fa-search-plus"></i></a>
                            #if(ALLOW_TO_EDIT == 1){#       
                            <a class="confirmation btn-delete" href="` + document.deleteLink + `/#: LOAN_REQUEST_ID #" id="bs_#:LOAN_REQUEST_ID #" style="height:17px;">
        <i class="fa fa-trash-o"></i></a>#}#
          </span>`
                }
            ]
        }
        );



        app.searchTable('loanTable', ['LOAN_NAME', 'REQUESTED_DATE', 'REQUESTED_DATE_N', 'LOAN_DATE', 'LOAN_DATE_N', 'REQUESTED_AMOUNT', 'STATUS']);

        app.pdfExport(
                'loanTable',
                {
                    'LOAN_NAME': 'Loan',
                    'REQUESTED_DATE': 'Request Date(AD)',
                    'REQUESTED_DATE_N': 'Request Date(BS)',
                    'LOAN_DATE': 'Loan Date(AD)',
                    'LOAN_DATE_N': 'Loan Date(BS)',
                    'REQUESTED_AMOUNT': 'Request Amt',
                    'STATUS': 'Status',
                    'REASON': 'Reason',
                    'RECOMMENDER_NAME': 'Recommender',
                    'APPROVER_NAME': 'Approver',
                    'RECOMMENDED_REMARKS': 'Recommended Remarks',
                    'RECOMMENDED_DATE': 'Recommended Dt',
                    'APPROVED_REMARKS': 'Approved Remarks',
                    'APPROVED_DATE': 'Approved Date'
                }
        );

        function gridDataBound(e) {
            var grid = e.sender;
            if (grid.dataSource.total() == 0) {
                var colCount = grid.columns.length;
                $(e.sender.wrapper)
                        .find('tbody')
                        .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
            }
        }
        ;
        $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Loan Name"},
                        {value: "Applied Date(AD)"},
                        {value: "Applied Date(BS)"},
                        {value: "Loan Date(AD)"},
                        {value: "Loan Date(BS)"},
                        {value: "Requested Amount"},
                        {value: "Status"},
                        {value: "Reason"},
                        {value: "Recommender"},
                        {value: "Approver"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"}
                    ]
                }];
            var dataSource = $("#loanTable").data("kendoGrid").dataSource;
            var filteredDataSource = new kendo.data.DataSource({
                data: dataSource.data(),
                filter: dataSource.filter()
            });

            filteredDataSource.read();
            var data = filteredDataSource.view();

            for (var i = 0; i < data.length; i++) {
                var dataItem = data[i];
                rows.push({
                    cells: [
                        {value: dataItem.LOAN_NAME},
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.REQUESTED_DATE_N},
                        {value: dataItem.LOAN_DATE},
                        {value: dataItem.LOAN_DATE_N},
                        {value: dataItem.REQUESTED_AMOUNT},
                        {value: dataItem.STATUS},
                        {value: dataItem.REASON},
                        {value: dataItem.RECOMMENDER_NAME},
                        {value: dataItem.APPROVER_NAME},
                        {value: dataItem.RECOMMENDED_REMARKS},
                        {value: dataItem.RECOMMENDED_DATE},
                        {value: dataItem.APPROVED_REMARKS},
                        {value: dataItem.APPROVED_DATE}
                    ]
                });
            }
            excelExport(rows);
            e.preventDefault();
        });

        function excelExport(rows) {
            var workbook = new kendo.ooxml.Workbook({
                sheets: [
                    {
                        columns: [
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true}
                        ],
                        title: "Loan Request",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "LoanRequestList.xlsx"});
        }
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);