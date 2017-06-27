(function ($) {
    'use strict';
    $(document).ready(function () {
        console.log(document.loanApprove);
        $("#loanApproveTable").kendoGrid({
            excel: {
                fileName: "LoanRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.loanApprove,
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
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "FULL_NAME", title: "Employee", width: 150},
                {field: "LOAN_NAME", title: "Loan", width: 120},
                {field: "REQUESTED_DATE", title: "Requested Date", width: 140},
                {field: "LOAN_DATE", title: "Loan Date", width: 100},
                {field: "REQUESTED_AMOUNT", title: "Requested Amount", width: 120},
                {field: "YOUR_ROLE", title: "Your Role", width: 120},
                {title: "Action", width: 70}
            ]
        });
        
        app.searchTable('loanApproveTable',['FULL_NAME','LOAN_NAME','REQUESTED_DATE','LOAN_DATE','REQUESTED_AMOUNT','YOUR_ROLE']);
        
        app.pdfExport(
                'loanApproveTable',
                {
                    'FULL_NAME': 'Name',
                    'LOAN_NAME': 'Loan',
                    'REQUESTED_DATE': 'Request Date',
                    'LOAN_DATE': 'Loan Date',
                    'REQUESTED_AMOUNT': 'Request Amt',
                    'YOUR_ROLE': 'Role',
                    'STATUS': 'Status',
                    'REASON': 'Reason',
                    'RECOMMENDED_REMARKS': 'Recommended Remarks',
                    'RECOMMENDED_DATE': 'Recommended Date',
                    'APPROVED_REMARKS': 'Approved Remarks',
                    'APPROVED_DATE': 'Approved Date'
                    
                });
                
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
                        {value: "Employee Name"},
                        {value: "Loan Name"},
                        {value: "Requested Date"},
                        {value: "Loan Date"},
                        {value: "Requested Amount"},
                        {value: "Your Role"},
                        {value: "Status"},
                        {value: "Reason"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"}
                    ]
                }];
            var dataSource = $("#loanApproveTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.FULL_NAME},
                        {value: dataItem.LOAN_NAME},
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.LOAN_DATE},
                        {value: dataItem.REQUESTED_AMOUNT},
                        {value: dataItem.YOUR_ROLE},
                        {value: dataItem.STATUS},
                        {value: dataItem.REASON},
                        {value: dataItem.RECOMMENDED_REMARKS},
                        {value: dataItem.RECOMMENDED_DATE},
                        {value: dataItem.APPROVED_REMARKS},
                        {value: dataItem.APPROVED_DATE},
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
                            {autoWidth: true}
                        ],
                        title: "Loan Request List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "LoanRequestList.xlsx"});
        }
    });
})(window.jQuery, window.app);
