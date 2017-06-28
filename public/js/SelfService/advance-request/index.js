(function ($, app) {
    'use strict';
    $(document).ready(function () {
        console.log(document.advanceRequestList);
        $("#advanceTable").kendoGrid({
            excel: {
                fileName: "AdvanceRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.advanceRequestList,
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
                {field: "ADVANCE_NAME", title: "Advance Name"},
                {field: "REQUESTED_DATE", title: "Applied Date"},
                {field: "ADVANCE_DATE", title: "Advance Date"},
                {field: "REQUESTED_AMOUNT", title: "Request Amount"},
                {field: "TERMS", title: "Terms(in month)"},
                {field: "STATUS", title: "Status"},
                {title: "Action"}
            ]
        });
        
        app.searchTable('advanceTable',['ADVANCE_NAME','REQUESTED_DATE','ADVANCE_DATE','REQUESTED_AMOUNT','TERMS','STATUS']);
        
        app.pdfExport(
                        'advanceTable',
                        {
                            'ADVANCE_NAME': 'Advance',
                            'REQUESTED_DATE': 'Request Dt',
                            'ADVANCE_DATE': 'Advance Dt',
                            'REQUESTED_AMOUNT': 'Request Amt',
                            'TERMS': 'Terms',
                            'STATUS': 'Status',
                            'REASON': 'Reason',
                            'RECOMMENDER_NAME': 'Recommender',
                            'APPROVER_NAME': 'Approver',
                            'RECOMMENDED_REMARKS': 'Reccommended Remarks',
                            'RECOMMENDED_DATE': 'Recommended Dt',
                            'APPROVED_REMARKS': 'Approved Remarks',
                            'APPROVED_DATE': 'Approved Dt'
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
                        {value: "Advance Name"},
                        {value: "Applied Date"},
                        {value: "Advance Date"},
                        {value: "Requested Amount"},
                        {value: "Terms(in month)"},
                        {value: "Status"},
                        {value: "Reason"},
                        {value: "Recommender"},
                        {value: "Approver"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"},
                    ]
                }];
            var dataSource = $("#advanceTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.ADVANCE_NAME},
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.ADVANCE_DATE},
                        {value: dataItem.REQUESTED_AMOUNT},
                        {value: dataItem.TERMS},
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
                            {autoWidth: true}
                        ],
                        title: "Advance Request",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AdvanceRequestList.xlsx"});
        }
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);