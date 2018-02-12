(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');

        app.initializeKendoGrid($table, [
            {field: "COMPANY_NAME", title: "Company Name"},
            {field: "NO_OF_DAYS", title: "No of Days"},
            {field: "NO_OF_DAYS", title: "Action"}
        ]);
        app.searchTable($table, ['COMPANY_NAME']);
        var exportMap = {
            'COMPANY_NAME': 'Leave',
            'NO_OF_DAYS': 'Total Days',
            'MONTH_EDESC': 'Month'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Penalty List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Penalty List');
        });
        var months = null;
        var $year = $('#fiscalYear');
        var $month = $('#fiscalMonth');
        app.setFiscalMonth($year, $month, function (yearList, monthList, currentMonth) {
            months = monthList;
        });


        $month.on('change', function () {
            var value = $(this).val();
            if (value == null) {
                return;
            }
            var selectedMonthList = months.filter(function (item) {
                return item['MONTH_ID'] === value;
            });
            if (selectedMonthList.length <= 0) {
                return;
            }
            app.serverRequest("", {fiscalYearId: selectedMonthList[0]['FISCAL_YEAR_ID'], fiscalYearMonthNo: selectedMonthList[0]['FISCAL_YEAR_MONTH_NO']}).then(function (response) {
                app.renderKendoGrid($table, response.data);
            }, function (error) {

            });
        });


    });
})(window.jQuery, window.app);
