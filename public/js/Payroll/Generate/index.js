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


        var columns = [
            {field: "EMPLOYEE_NAME", title: "Employee", width: 150, locked: true},
        ];

        var addtion = {title: "Additions", columns: []};
        var deduction = {title: "Deductions", columns: []};
        $.each(data.ruleList, function (key, value) {
            if (value['PAY_TYPE_FLAG' ] == 'A') {
                addtion.columns.push({field: "P_" + value['PAY_ID'], title: value['PAY_EDESC'], width: 150});
            } else {
                deduction.columns.push({field: "P_" + value['PAY_ID'], title: value['PAY_EDESC'], width: 150});
            }
        });
        columns.push(addtion);
        columns.push(deduction);
        app.initializeKendoGrid($table, columns);
    });
})(window.jQuery, window.app);


