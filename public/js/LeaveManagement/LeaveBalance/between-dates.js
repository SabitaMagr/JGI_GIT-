(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $table = $("#table");
        var $search = $('#search');
//        var columns = [
//            {field: "EMPLOYEE_CODE", title: "Code", width: 150, locked: true},
//            {field: "FULL_NAME", title: "Employee", width: 150, locked: true},
//        ];
        var map = {
            'EMPLOYEE_CODE': 'Code',
            'FULL_NAME': 'Name'
        };


        var $leave = $('#leaveId');
        var leaveList = document.leaves;
        app.populateSelect($leave, document.leaves, 'LEAVE_ID', 'LEAVE_ENAME');


        function reinitializeKendo(optionalColumns) {
            console.log(optionalColumns);
            var columns = [
                {field: "EMPLOYEE_CODE", title: "Code", width: 150},
                {field: "FULL_NAME", title: "Employee", width: 150},
            ];
            map = {
            'EMPLOYEE_CODE': 'Code',
            'FULL_NAME': 'Name'
        };
            var columnsList;
            $.each(optionalColumns, function (i, val) {

                columnsList = {
                    title: leaveList[i]['LEAVE_ENAME'],
                    columns: [
                        {
                            title: 'TAKEN',
                            field: 'L' + val + '_' + 'TAKEN',
                            width: 100
                        },
                        {
                            title: 'BALANCE',
                            field: 'L' + val + '_' + 'BALANCE',
                            width: 100
                        },
                    ]
                };
                  map['L' + val + 'TAKEN'] = leaveList[i]['LEAVE_ENAME'] + 'TAKEN';
                  map['L' + val + 'BALANCE'] = leaveList[i]['LEAVE_ENAME'] + 'BALANCE';
                columns.push(columnsList);
                
            });
            app.initializeKendoGrid($table, columns);


//            var columnsList;
//            for (var i in optionalColumns) {
//                console.log(i);
//                columnsList = {
//                    title: leaveList[i]['LEAVE_ENAME'],
//                    columns: [
//                        {
//                            title: 'TAKEN',
//                            field: 'L' + leaveList[i]['LEAVE_ID'] + '_' + 'TAKEN',
//                            width: 100
//                        },
//                        {
//                            title: 'BALANCE',
//                            field: 'L' + leaveList[i]['LEAVE_ID'] + '_' + 'BALANCE',
//                            width: 100
//                        },
//                    ]
//                };

//                columns.push(columnsList);
//
//                if(optionalColumns.indexOf("1") != -1){
//                    map['L' + leaveList[i]['LEAVE_ID'] + '_' + 'TOTAL'] = leaveList[i]['LEAVE_ENAME'] + '(Total)';
//                }
//                if(optionalColumns.indexOf("2") != -1){
//                    map['L' + leaveList[i]['LEAVE_ID'] + '_' + 'TAKEN'] = leaveList[i]['LEAVE_ENAME'] + '(Taken)';
//                }
//            
//                map['L' + leaveList[i]['LEAVE_ID'] + '_' + 'BALANCE'] = leaveList[i]['LEAVE_ENAME'] + '(Balance)';
//            } 

        }
//        reinitializeKendo([]);
//        app.initializeKendoGrid($table, columns);
        app.searchTable($table, ['EMPLOYEE_CODE','FULL_NAME']);

        $search.on('click', function () {
            var leaveColumns = $leave.val();
            $table.empty();
            reinitializeKendo(leaveColumns);
//            if(leaveColumns != null){ reinitializeKendo(optionalColumns); }
//            else{ reinitializeKendo([]); }
//            app.initializeKendoGrid($table, columns);

            var q = document.searchManager.getSearchValues();
            q['fromDate'] = $('#fromDate').val();
            q['toDate'] = $('#toDate').val();
            console.log(q);
            App.blockUI({target: "#hris-page-content"});
            app.pullDataById(document.pullBalanceBetweenDates, q).then(function (success) {
                App.unblockUI("#hris-page-content");
                console.log(success.data);
                app.renderKendoGrid($table, success.data);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });

        $('#excelExport').on("click", function () {
            app.excelExport($table, map, "Employee Leave Balance Report.xlsx");
        });
        $('#pdfExport').on("click", function () {
            app.exportToPDF($table, map, "Employee Leave Balance Report.pdf", 'A2');
        });

    });
})(window.jQuery, window.app);
