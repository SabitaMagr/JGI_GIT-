(function ($) {
    'use strict';
    $(document).ready(function () {    
        console.log(document.leaves);
        $("#leaveTable").kendoGrid({
            excel: {
                fileName: "LeaveList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.leaves,
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
            dataBound:gridDataBound,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
//                {field: "LEAVE_CODE", title: "Leave Code"},
                {field: "LEAVE_ENAME", title: "Leave"},
                {field: "COMPANY_NAME", title: "Company"},
                {field: "ALLOW_HALFDAY", title: "Allow Halfday"},
                {field: "DEFAULT_DAYS", title: "Default Days"},
                {field: "CARRY_FORWARD", title: "Carry Forward"},
                {field: "CASHABLE", title: "Cashable"},
                {field: "PAID", title: "Paid"},
                {title: "Action"}
            ]
        }); 
        
        app.searchTable('leaveTable',['LEAVE_ENAME','COMPANY_NAME','ALLOW_HALFDAY','DEFAULT_DAYS','CARRY_FORWARD','CASHABLE','PAID']);
        
        app.pdfExport(
                'leaveTable',
                {
                    'LEAVE_ENAME': 'Leave',
                    'COMPANY_NAME': 'Company',
                    'ALLOW_HALFDAY': 'Allow Halfday',
                    'DEFAULT_DAYS': 'Default Days',
                    'CARRY_FORWARD': 'Carry Forward',
                    'CASHABLE': 'Cashable',
                    'PAID': 'Paid'
                });
        
        function gridDataBound(e) {
            var grid = e.sender;
            if (grid.dataSource.total() == 0) {
                var colCount = grid.columns.length;
                $(e.sender.wrapper)
                        .find('tbody')
                        .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
            }
        };
        
        $("#export").click(function (e) {
            var grid = $("#leaveTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    
    });   
})(window.jQuery, window.app);
