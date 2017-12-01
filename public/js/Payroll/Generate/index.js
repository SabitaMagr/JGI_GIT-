(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var data = document.data;

        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
        var $table = $('#table');

        app.populateSelect($fiscalYear, data.fiscalYearList, "FISCAL_YEAR_ID", "FISCAL_YEAR_NAME", "Select Fiscal Year");
        app.populateSelect($month, [], "MONTH_ID", "MONTH_EDESC", "Select Month");

        $fiscalYear.on('change', function () {
            var value = $(this).val();
            var filteredMonths = [];
            if (value != -1) {
                var filteredMonths = data.monthList.filter(function (item) {
                    return item['FISCAL_YEAR_ID'] == value;
                });
            }
            app.populateSelect($month, filteredMonths, "MONTH_ID", "MONTH_EDESC", "Select Month");
        });

        

    });
})(window.jQuery, window.app);


