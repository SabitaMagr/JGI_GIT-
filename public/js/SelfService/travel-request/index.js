(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("#travelTable").kendoGrid({
            excel: {
                fileName: "TravelRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.travelRequestList,
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
                {field: "FROM_DATE", title: "Start Date",width:120},
                {field: "FROM_DATE_NEP", title: "Start Date Nep",width:120},
                {field: "TO_DATE", title: "To Date",width:120},
                {field: "TO_DATE_NEP", title: "To Date Nep",width:120},
                {field: "REQUESTED_DATE", title: "Applied Date",width:120},
                {field: "REQUESTED_DATE_NEP", title: "Applied Date Nep",width:120},
                {field: "DESTINATION", title: "Destination",width:120},
                {field: "REQUESTED_AMOUNT", title: "Request Amt.",width:120},
                {field: "REQUESTED_TYPE", title: "Request For",width:120},
                {field: "STATUS", title: "Status",width:100},
                {title: "Action",width:110}
            ]
        });
        
        app.searchTable('travelTable',['FROM_DATE','TO_DATE','REQUESTED_DATE','DESTINATION','REQUESTED_AMOUNT','REQUESTED_TYPE','STATUS']);
        
        app.pdfExport(
                        'travelTable',
                        {
                            'FROM_DATE': 'From Date',
                            'TO_DATE': 'To Date',
                            'REQUESTED_DATE': 'Request Date',
                            'DESTINATION': 'Destination',
                            'REQUESTED_AMOUNT': 'Request Amt',
                            'REQUESTED_TYPE': 'Request Type',
                            'STATUS': 'Status',
                            'PURPOSE': 'Purpose',
                            'REMARKS': 'Remarks',
                            'RECOMMENDER_NAME': 'Recommender',
                            'APPROVER_NAME': 'Approver',
                            'RECOMMENDED_REMARKS': 'Recommended Remarks',
                            'RECOMMENDED_DATE': 'Recommended Date',
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
                        {value: "From Date"},
                        {value: "To Date"},
                        {value: "Applied Date"},
                        {value: "Destination"},
                        {value: "Requested Amount"},
                        {value: "Requested Type"},
                        {value: "Status"},
                        {value: "Purpose"},
                        {value: "Remarks"},
                        {value: "Recommender"},
                        {value: "Approver"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"}
                    ]
                }];
            var dataSource = $("#travelTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.FROM_DATE},
                        {value: dataItem.TO_DATE},
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.DESTINATION},
                        {value: dataItem.REQUESTED_AMOUNT},
                        {value: dataItem.REQUESTED_TYPE},
                        {value: dataItem.STATUS},
                        {value: dataItem.PURPOSE},
                        {value: dataItem.REMARKS},
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
                            {autoWidth: true},
                            {autoWidth: true}
                            
                        ],
                        title: "Travel Request",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "TravelRequestList.xlsx"});
        }
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);


angular.module("hris",[])
        .controller("travelRequestList",function($scope,$http,$window){
            $scope.msg =  $window.localStorage.getItem("msg");
            if($window.localStorage.getItem("msg")){
                window.toastr.success($scope.msg, "Notifications");
            }
            $window.localStorage.removeItem("msg");
});