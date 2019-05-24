(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('Select').select2();

        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
        var $table = $('#table');
        var map = {};
         var exportType = {
            "ACCOUNT_NO": "STRING",
        };






        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });





        $('#searchEmployeesBtn').on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fiscalId'] = $fiscalYear.val();
            q['monthId'] = $month.val();
//            console.log(q);

            app.serverRequest(document.pullMonthlySummaryLink, q).then(function (response) {
                if (response.success) {
                    console.log(response);
                    renderView(response.data.additionDetail,response.data.additionDetail)
//                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });


        });
        
        
        var renderView= function(addtion,deduction){
            
        }

//
//
//        $('#excelExport').on('click', function () {
//            app.excelExport($table, map, 'GroupSheet.xlsx',exportType);
//        });
//        $('#pdfExport').on('click', function () {
//            app.exportToPDF($table, map, 'GroupSheet.pdf');
//        });






    });
})(window.jQuery, window.app);


