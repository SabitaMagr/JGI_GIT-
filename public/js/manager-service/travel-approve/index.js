(function ($) {
    'use strict';
    $(document).ready(function () {
        console.log(document.travelApprove);
        $("#travelApproveTable").kendoGrid({
            excel: {
                fileName: "TravelRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.travelApprove,
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
                {field: "FIRST_NAME", title: "Employee Name", width: 140},
                {field: "TRAVEL_CODE", title: "Travel Code", width: 120},
                {field: "FROM_DATE", title: "From Date", width: 120},
                {field: "TO_DATE", title: "To Date", width: 100},
                {field: "REQUESTED_DATE", title: "Requested Date", width: 140},
                {field: "DESTINATION", title: "Destination", width: 110},
                {field: "REQUESTED_AMOUNT", title: "Requested Amt.", width: 140},
                {field: "REQUESTED_TYPE", title: "Request For", width: 120},
                {title: "Action", width: 80}
            ]
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
                        {value: "Travel Code"},
                        {value: "From Date"},
                        {value: "To Date"},
                        {value: "Requested Date"},
                        {value: "Destination"},
                        {value: "Purpose"},
                        {value: "Requested Amount"},
                        {value: "Request For"},
                        {value: "Your Role"},
                        {value: "Status"},
                        {value: "Remarks By Employee"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"}
                    ]
                }];
            var dataSource = $("#travelApproveTable").data("kendoGrid").dataSource;
            var filteredDataSource = new kendo.data.DataSource({
                data: dataSource.data(),
                filter: dataSource.filter()
            });

            filteredDataSource.read();
            var data = filteredDataSource.view();

            for (var i = 0; i < data.length; i++) {
                var dataItem = data[i];
                var middleName = dataItem.MIDDLE_NAME != null ? " " + dataItem.MIDDLE_NAME + " " : " ";

                rows.push({
                    cells: [
                        {value: dataItem.FIRST_NAME + middleName + dataItem.LAST_NAME},
                        {value: dataItem.TRAVEL_CODE},
                        {value: dataItem.FROM_DATE},
                        {value: dataItem.TO_DATE},
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.DESTINATION},
                        {value: dataItem.PURPOSE},
                        {value: dataItem.REQUESTED_AMOUNT},
                        {value: dataItem.REQUESTED_TYPE},
                        {value: dataItem.YOUR_ROLE},
                        {value: dataItem.STATUS},
                        {value: dataItem.REMARKS},
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
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true}
                        ],
                        title: "Travel Request List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "TravelRequestList.xlsx"});
        }
    });
})(window.jQuery, window.app);
