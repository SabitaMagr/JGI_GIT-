
function setTemplate(temp) {
    var returnvalue = '';
    if (temp == 'PR') {
        returnvalue = 'blue';
    } 
    else if (temp == 'AB') {
        returnvalue = 'red';
    } else if (temp == 'LV') {
        returnvalue = 'green';
    } else if (temp == 'DO') {
        returnvalue = 'yellow';
    } else if (temp == 'HD') {
        returnvalue = 'purple';
    } else if (temp == 'WD') {
        returnvalue = 'purple-soft';
    } else if (temp == 'WH') {
        returnvalue = 'yellow-soft';
    }
    return returnvalue;
}


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
                var columns=generateColsForKendo();
                
                
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
                
                app.renderKendoGrid($table, response.data);
                
            }, function (error) {

            });
        });
        
        
        function generateColsForKendo() {
              exportVals={
            'FULL_NAME': 'Employee Name',
            'FOOD_ALLOWANCE': 'Food Allowance',
            'SHIFT_ALLOWANCE': 'Shift Allowance',
            'NIGHT_SHIFT_ALLOWANCE': 'Night Shift Allowance',
        };
            var cols = [];
            cols.push({
                field: 'FULL_NAME',
                title: "Name",
//                locked: true,
                template: '<span>#:FULL_NAME#</span>',
//                width: 150
            });
//            for (var i = 1; i <= dayCount; i++) {
//                var temp = 'D' + i;
//                exportVals[temp]=i;
//                cols.push({
//                    field: temp,
//                    title: "" + i,
//                     width: 60,
//                     template: '<button type="button" class="btn btn-block #:setTemplate('+temp+')#">#:(' + temp + ' == null) ? " " :'+temp+'#</button>',
////                     template: '<span  class="#: setTemplate(' + temp + ') #">#:(' + temp + ' == null) ? " " :'+temp+'#</span>',
//                });
//            }
            
            
            cols.push({
                field: 'FOOD_ALLOWANCE',
                title: "Food Allowance",
                template: '<span>#:FOOD_ALLOWANCE#</span>',
//                width: 100
            });
            cols.push({
                field: 'SHIFT_ALLOWANCE',
                title: "Shift Allowance",
                template: '<span>#:SHIFT_ALLOWANCE#</span>',
//                width: 100
            });
            cols.push({
                field: 'NIGHT_SHIFT_ALLOWANCE',
                title: "Night Shift Allowance",
                template: '<span>#:NIGHT_SHIFT_ALLOWANCE#</span>',
//                width: 100
            });
            
//            console.log(exportVals);
            return cols;
        }
        
        
        
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportVals, 'Employee_Wise_Allowance_Report');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportVals, 'Employee_Wise_Allowance_Report');
        });





    });
})(window.jQuery, window.app);