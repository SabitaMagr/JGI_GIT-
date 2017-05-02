(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("#loanTable").kendoGrid({
            dataSource: {
                data: document.loans,
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
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
//                {field: "LOAN_CODE", title: "Loan Code",width:80},
                {field: "LOAN_NAME", title: "Loan",width:130},
                {field: "COMPANY_NAME", title: "Company",width:110},
                {field: "MIN_AMOUNT", title: "Amount Range",width:110},
                {field: "INTEREST_RATE", title: "Interest Rate",width:110},
                {field: "REPAYMENT_AMOUNT", title: "Repayment Amount",width:130},
                {field: "REPAYMENT_PERIOD", title: "Repayment(In Month)",width:150},
                {title: "Action",width:110}
            ]
        });
        
        $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Loan Code"},
                        {value: "Loan Name"},
                        {value: "Amount Range"},
                        {value: "Interest Rate"},
                        {value: "Repayment Amount"},
                        {value: "Repayment Period"},
                        {value: "Remarks"}
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
                        {value: dataItem.LOAN_CODE},
                        {value: dataItem.LOAN_NAME},
                        {value: dataItem.MIN_AMOUNT+"-"+dataItem.MAX_AMOUNT},
                        {value: dataItem.INTEREST_RATE+"%"},
                        {value: dataItem.REPAYMENT_AMOUNT},
                        {value: dataItem.REPAYMENT_PERIOD},
                        {value: dataItem.REMARKS}
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
                            {autoWidth: true}
                        ],
                        title: "Loan",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "LoanList.xlsx"});
        }
        
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);