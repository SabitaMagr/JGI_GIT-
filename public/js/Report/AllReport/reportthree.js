(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        var $table = $("#report");
        
        var exportVals;
//        var exportVals={
//            'FULL_NAME': 'Employee Name',
//        };


        var months = null;
        var $year = $('#fiscalYearId');
        var $month = $('#monthId');
        app.setFiscalMonth($year, $month, function (yearList, monthList, currentMonth) {
            months = monthList;
        });

        var $search = $('#search');
        $search.on('click', function () {
            var data = document.searchManager.getSearchValues();
            data['monthCodeId'] = $month.val();
            app.serverRequest('', data).then(function (response) {
                var monthDays=response.data.monthDetail.DAYS;
                var columns=generateColsForKendo(monthDays);
                
                
                $table.empty();
                
                $table.kendoGrid({
                    height: 450,
                    scrollable: true,
                    columns: columns,
                dataBound: function (e) {
                    var grid = e.sender;
                    if (grid.dataSource.total() === 0) {
                        var colCount = grid.columns.length;
                        $(e.sender.wrapper)
                                .find('tbody')
                                .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
                    }
                },
                pageable: {
                    refresh: true,
                    pageSizes: true,
                    buttonCount: 5
                },
                });
                
                app.renderKendoGrid($table, response.data.data);
                
            }, function (error) {

            });
        });
        
        
        function generateColsForKendo(dayCount) {
              exportVals={
            'FULL_NAME': 'Employee Name',
        };
//        exportVals
            var cols = [];
            cols.push({
                field: 'FULL_NAME',
                title: "Name",
                locked: true,
                template: '<span>#:FULL_NAME#</span>',
                width: 150
            });
            for (var i = 1; i <= dayCount; i++) {
                var temp = 'D' + i;
                exportVals[temp]=i;
                cols.push({
                    field: temp,
                    title: "" + i,
                     width: 35,
                     template: '<span>#:(' + temp + ' == null) ? " " :'+temp+'#</span>',
                });
            }
            console.log(exportVals);
            return cols;
        }
        
        
        
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportVals, 'Employee_Wise_Attendance_Report');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportVals, 'Employee_Wise_Attendance_Report');
        });





    });
})(window.jQuery, window.app);